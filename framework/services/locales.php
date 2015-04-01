<?php

    namespace Framework\Services;

    use Framework\Core\Cache;

    /**
     * Get translation from locales
     *
    **/
    class Locales {

        /**
         * @var array    $locales   Locales collection
        **/
        private static $locales     = array();

        /**
         * @var string   $parsers   Translation parsers collection
        **/
        private $parsers            = array();

        /**
         * @var string  $language    Language code
        **/
        private $language;

        /**
         * @const Relative locales path
        **/
        const PATH_LOCALES            = '/locales';


        /**
         * Initialize locales parameters
         * @param string    $language       Language code
        **/
        public function __construct($language) {
            $this->language = $language;
            setLocale(LC_ALL, $language.'.UTF-8');
        }

        /**
         * Set a custom translation data parser
         * @param string    $name       Parser name
         * @param Closure   $parser     Parser callback
        **/
        public function parser($name, \Closure $parser) {
            $this->parsers[$name] = $parser;
        }


        /**
         * Get a translation
         * @param string    $locale         Translation path (file->path.to.my.translation)
         * @param array     $values         Data to merge in the translation
        **/
        public function translate($locale, array $values = array()) {

            $file = strstr($locale, '->', true);
            $path = substr(strstr($locale, '->', false), 2);

            if(
                (
                    !isset(self::$locales[$this->language])
                    || !isset(self::$locales[$this->language][$file])
                )
                && !$this->_load($file)
            ) {
                trigger_error('Undefined locale "'.$locale.'" ('.$this->language.')', E_USER_WARNING);
                return $locale;
            }

            // Get translation
            $translation = array_value($path, self::$locales[$this->language][$file], $path);

            $self = $this;

            // Data parser ( [function:input|param1|param2|...] )
            $translation = preg_replace_callback('#\[([a-z0-9_]+):([a-z0-9]+)(\|.+){0,}\]#U', function($m) use($self, $values) {

                // No parser avalaible
                if(!isset($self->parsers[ $m[1] ])) {
                    trigger_error('Not callable locale translation parser '.$m[1], E_USER_WARNING);
                    return $m[0];
                }

                // No variable value available
                if(!isset($values[ $m[2] ]))
                    return $m[0];


                // Set parser parameters
                if(!empty($m[3])) {
                    $parameters = explode('|', substr($m[3], 1));
                    $parameters = array_reverse($parameters);
                }
                else {
                    $parameters = array();
                }

                // Add data value
                array_push($parameters, $values[ $m[2] ]);

                return call_user_func_array( $self->parsers[ $m[1] ], array_reverse($parameters));

            }, $translation);


            // Replace variable ( :input )
            $translation = preg_replace_callback('#:([a-z0-9]+)#', function($m) use($values) {

                if(isset($values[ $m[1] ]))
                    return $values[ $m[1] ];

                return $m[0];

            }, $translation);


            // Replace reference ( &[file->node.get.value] or &[this->node.get.value] )
            $translation = preg_replace_callback('#&\[(.+)->(.+)\]#U', function($m) use($self, $file) {
                $file = str_replace('this', $file, $m[1]);
                $reference = $self->translate($file.'->'.$m[2]);
                return (($reference == $m[1]) ? $m[0] : $reference);

            }, $translation);


            return $translation;
        }


        /**
         * Load a locale before parsing and caching it
         * @param   string      $file       Path of the locale to load
         * @return  Returns weither the locale have been loaded or not
        **/
        private function _load($file) {

            $source = root(self::PATH_LOCALES.'/'.$this->language."/$file.properties");
            $cache = new Cache('locales/'.$this->language.'/'.str_replace('/', '.', $file).'.json');

            // Read cache content
            if($cache->exists() && $cache->recent($source)) {
                self::$locales[$this->language][$file] = json_decode($cache->get() ,true);
                return true;
            }
            elseif(file_exists($source)) {

                $handle = fopen($source, 'r');
                if($handle):
                    while(!feof($handle)):
                        $line = trim(fgets($handle));
                        $beginwith = substr($line, 0, 1);
                        if($beginwith != '#' && $beginwith != '!'):
                            $path = trim(str_replace('.', "']['", strstr(trim(addslashes($line)), '=', true))); // Property name
                            $value = trim(addslashes(str_replace('=', '', strstr($line, '=', false)))); // Property value
                            $value = str_replace(array('\\\\r\\\\n', '\\\\r', '\\\\n'), PHP_EOL, $value); // Allow breaklines in value

                            $data = array();
                            eval("\$data['$path']='$value';");

                            $origin = (isset(self::$locales[$this->language][$file]) ? self::$locales[$this->language][$file] : array());
                            self::$locales[$this->language][$file] = array_merge_recursive($origin, $data);
                        endif;
                    endwhile;

                    fclose($handle);
                endif;

                $cache->put(json_encode(self::$locales[$this->language][$file]));

                return true;

            }

            else {
                return false;
            }

        }

    }
