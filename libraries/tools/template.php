<?php

    namespace Tools;

    class Template  {
        private $buffer;
        private $options = array();
                
        /**
         * Generate a new template object
        **/
        public function __construct(&$buffer) {
            $this->buffer = $buffer;
        }
        
        /**
         * Configure template object
        **/
        public function configure($options) {
            $this->options = $options;
        }
        
        /**
         * 
        **/
        public function parse(&$data, $options = array()) {        
            $output = $this->buffer;
            $boundary = '--'.sha1(uniqid(microtime(), true)).'--';
            
            // noparse;
            $output = preg_replace_callback('#\{noparse\}(.+)\{/noparse\}#isU', function($matches) use($boundary) {
                $escaped = str_replace(array('{', '}'), array("<!--[$boundary", "$boundary]-->"), $matches[1]);
                return "{noparse}$escaped{/noparse}";
            }, $output);
            
            $output = $this->comments($output); // Comments
            $output = $this->comments($this->zones($output, $data)); // Zones
            $output = $this->loops($output, $data); // Loops
            $output = $this->jumps($output, $data); // Jumps (for)
            $output = $this->variables($output, $data); // Variables
            $output = $this->locales($output, $data); // Locales
            $output = $this->constants($output); // Constants
            $output = $this->replacements($output, $data); // Replacements
            
             // noparse;
            $output = preg_replace_callback("#\{noparse\}(.+)\{/noparse\}#isU", function($matches) use($boundary) {
                return str_replace(array("<!--[$boundary", "$boundary]-->"), array('{', '}'), $matches[1]);
            }, $output);
            
            return $output; // Output
        }
        
        
        /**
         * Comments parser
         * {* this text will not be viewed *}
        **/
        protected function comments($buffer) {
            return preg_replace('#\{\*(.+)\*\}#isU', null, $buffer);
        }
        
        /**
         * Zones parser
         * {inc "path/to/file"}
        **/
        protected function zones($buffer, &$data) {
             return preg_replace_callback('#\{zone "(.+)"\}#isU', function($matches) use($data) {
                $path = preg_replace_callback('#\$([a-z0-9\._]+)#is', function($matches) use($data) {
                    $path = implode("']['", explode('.', $matches[1]));
                    @eval('$var = $data[\''.$path.'\'];');
                    return  (!empty($var) ? $var : $matches[0]);                                                                     
                }, $matches[1]);
                $path = PATH_TEMPLATES . "/$path.php";
                
                if(file_exists(root($path))):
                    ob_start();
                    extract($data);
                    include(root($path));
                    $zone = ob_get_flush();
                    ob_clean();
                    $template = new Template($zone);
                    return $template->parse($data);
                else:
                    Console::log("Can't include zone '$path'", Console::LOG_TEMPLATE);
                 endif;
                 
            }, $buffer);
        }
        
        
        /**
         * Loops parser
         *  {loop $array} {$key} : {$value} {/loop}
        **/
        protected function loops($buffer, &$data) {
            return preg_replace_callback('#\{loop \$([a-z0-9\._]+)\}(.+)\{/loop}#isU', function($matches) use($data) {
                $path = implode("']['", explode('.', $matches[1]));
                @eval('$var = $data[\''.$path.'\'];');
                $output = null;
                
                if(!empty($var) && is_array($var)):
                    foreach($var as $key => $value) {
                        $new = str_replace('{$key}', $key, $matches[2]);
                        
                        if(is_array($value)):
                            $new = preg_replace_callback('#\{\$value(\..+)\}#isU', function($matches) use($data) {
                                $path = implode("']['", explode('.', $matches[1]));
                                @eval('$var = $data[\''.$path.'\'];');
                                return $var;
                            }, $new);
                            
                        else:
                            $new = str_replace('{$value}', $value, $new);
                        endif;
                        
                        $output .= $new;
                    }
                    return $output;
                else:
                    Console::log("Variable \$".$matches[1]." can't be used in a loop (not an array)", Console::LOG_TEMPLATE);
                endif;
            }, $buffer);
        }
        
        
        /**
         * Jumps (for) parser
         * {jump 12 => 1234 / 5} {$step} / {$i} {/jump}
         * {jump 0 => 123 [/ 1]} {$step} {$i} {/jump}
        **/
        protected function jumps($buffer, &$data) {
            return preg_replace_callback('#\{jump ([0-9\.]+) ?=> ?([0-9\.]+) ?(/ ?([0-9\.]+))?\}(.+)\{/jump}#isU', function($matches) use($data) {
                $start = floatval(trim($matches[1]));
                $stop = floatval(trim($matches[2]));
                $jump = trim($matches[4]);
                if(empty($jump)) $jump = 1;
                $output = null;
                
                if($start > $stop):
                    for($i = $start; $i>=$stop;):
                        $output .= str_replace(array('{$i}', '{$step}'), $i, $matches[5]);
                        @eval("\$i = \$i -  $jump;");
                    endfor;
                else:
                    for($i = $start; $i<=$stop;):
                        $output .= str_replace(array('{$i}', '{$step}', '{$this}'), $i, $matches[5]);
                        @eval("\$i = \$i +  $jump;");
                    endfor;
                endif;
                
                return $output;
                
            }, $buffer);
        }
        
        
        /**
         * Data replacement parsers
        **/
        protected function replacements($buffer, &$data) {
            return preg_replace_callback('#\{"(.+)" \$([a-z0-9\._]+)\}#isU', function($matches) use($data) {
                $path = implode("']['", explode('.', $matches[2]));
                @eval('$array = $data[\''.$path.'\'];');
                if(!empty($array) && is_array($array)):
                    $output = $matches[1];
                    foreach($array as $index => $value) {
                        $output = str_replace(":$index", $value, $output);
                    }
                    return $output;
                else:
                    Console::log("Variable '\$".$matches[2]."' is not an array", Console::LOG_TEMPLATE);
                endif;
                
            }, $buffer);
        }
        
        
        /**
         * Variables parser
         * {$variable}
        **/
        protected function variables($buffer, &$data) {
            return preg_replace_callback('#\{\$([a-z0-9\._]+)\}#isU', function($matches) use($data) {
                $path = implode("']['", explode('.', $matches[1]));
                @eval('$var = $data[\''.$path.'\'];');
                
                if(!empty($var) && is_string($var))
                    return $var;
                else
                    Console::log("Variable ".$matches[1]." not found", Console::LOG_TEMPLATE);
            }, $buffer);
        }
        
        
        /**
         * Constants parser
         * {#CONSTANT}
        **/
        protected function constants($buffer) {
            return preg_replace_callback('#\{\#([a-z0-9\\_]+)\}#isU', function($matches) {
                if(defined($matches[1]))
                    return constant($matches[1]);
                else
                    Console::log('Constant '.$matches[1].' not defined', Console::LOG_TEMPLATE);
            }, $buffer);
        }
        
        
        /**
         * Locales parser
         * {@file:array.item}
         * {@file:array.item $data}
        **/
        protected function locales($buffer, &$data) {
            return preg_replace_callback('#\{@(.+)( \$([a-z0-9\._]+))?\}#isU', function($matches) use($data) {
                @eval('$locale = $matches[1];');
                if(!empty($matches[3])):
                    $path = implode("']['", explode('.', $matches[3]));
                    @eval('$var = $data[\''.$path.'\'];');
                    return (is_array($var) ? Locales::_e($locale, $var) : null);
                else:
                    return Locales::_e($locale);
                endif;
            }, $buffer);
        }
        
        
        
        /**
         * Conditions parser;
         * {if $conditions} {else} {/endif}
         * {if $conditions} {elseif $conditions} {/if}
        **/
        /* [Disallowed]
        protected static function conditions($buffer) {
            return preg_replace_callback('#\{if (.+)\}(.+)(\{(else|elseif (.+))\}(.+))?\{(/if|endif)\}#isU', function($matches) {
                // [Security] To do : check unsafe functions
                
                $conditions = preg_replace_callback('#\$([a-z0-9\._]+)#is', function($m) {
                    $path = implode("']['", explode('.', $m[1]));
                    return 'Response::$data[\''.$path.'\']';
                }, $matches[1]);
                
                $conditions = preg_replace_callback('#\#([a-z0-9_]+)#is', function($m) {
                    return 'constant("'.$m[1].'")';
                }, $conditions);
                
                @eval('$assertion = ('.$conditions.');');
                
                if($assertion):
                    return $matches[2];
                else:
                    if($matches[4] == 'else'):
                        return $matches[6];
                    else:
                        $conditions = preg_replace_callback('#\$([a-z0-9\._]+)#is', function($m) {
                            $path = implode("']['", explode('.', $m[1]));
                            return 'Response::$data[\''.$path.'\']';
                        }, $matches[5]);
                        
                        $conditions = preg_replace_callback('#\#([a-z0-9_]+)#is', function($m) {
                            return 'constant("'.$m[1].'")';
                        }, $conditions);
                        @eval('$assertion = ('.$conditions.');');
                
                        if($assertion):
                            return $matches[6];
                        endif;
                    endif;
                endif;
            }, $buffer);
        }
        //*/
        
    }

?>