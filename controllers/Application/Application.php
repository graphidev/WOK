<?php

    namespace Controllers\Application;

    /**
     * the Application controller class provide
     * an abstracted class that instanciate defaults
     * application controllers informations.
    **/
    class Application extends \Controllers\Foundation\Http {

        protected $user;

        protected $translations;


        /**
         * Instanciate application controller
         * @param Services      $services           Application services collection
        **/
        public function __construct(\Application\Services $services) {

            parent::__construct($services);

            /** Application maintenance **/
            if($this->settings->environment->maintenance) {

                exit (call_user_func(
                    \Message\Response::html($this->_view('maintenance'), 503)
                ));

            }

            /** User meta informations **/
            $this->user = (object) array(
                'locale'     => array_shift($this->settings->application->locales),
                'tracking'   => !filter_var($this->request->getHeader('DNT', false), FILTER_VALIDATE_BOOLEAN),
                'time'       => time() // Cookie deposit time
            );

            // Override user meta (from cookie)
            if($cookie = $this->cookies->get('usermeta')) {

                parse_str($cookie, $userMetaData);

                if(!empty($userMetaData['time']) && is_numeric($userMetaData['time'])) {
                    $this->user->time = intval($userMetaData['time']);
                }


                if(!empty($userMetaData['locale']) && in_array($userMetaData['locale'], $this->settings->application->locales)) {
                    $this->user->locale = $userMetaData['locale'];
                }
                else { // Wrong user locale, retrieve a new one
                    $this->user->locale = $this->_getUserLocale($this->user->locale);
                }

                if(isset($userMetaData['tracking'])) {
                    $this->user->locale = filter_var($userMetaData['tracking'], FILTER_VALIDATE_BOOLEAN);
                }

            }

            // Retrieve user meta data from headers
            else {

                // Retrieve user locale
                $this->user->locale = $this->_getUserLocale($this->user->locale);

                // Retrieve services tracking
                $this->user->tracking = !filter_var($this->request->getHeader('DNT', false), FILTER_VALIDATE_BOOLEAN);

                // Wait for an other request (french legal context)
                if(!$this->session->has('user.hasAlreadyCome')) {
                    $this->session->set('user.hasAlreadyCome', true);
                }

                // Store user meta in a cookie
                else {
                    $elapsed = time() - $this->user->time;
                    $value = http_build_query((array) $this->user, null, '&');
                    $this->cookies->set('userinfos', $value, transtime('13months') - $elapsed);
                }

            }

            $this->translations = $services->get('locales', [$this->user->locale]);

        }


        /**
         * Generate default data used by the views
         * @param Closure       $callback           Custom additional data
        **/
        private function _initViewData(\Closure $callback = null) {

            $data = array();

            $data['page'] = (object) array(
                'title'     =>  'Project title',
                'lang'      => mb_substr($this->user->locale, 0, mb_strlen('_')+1)
            );

            /*

                ... Get your application default view data here ...

            */

            if(!empty($data)) {
                $tmp = call_user_func($callback, $data);
                $data = array_merge($tmp, $data); // Force keeping default values
            }

            return $data;

        }



        /**
         * HTML view generator helper
         * @param   string      $template           Template to insert data in
         * @param   string      $httpcode           HTTP response code
         * @param   Closure     $callback           Callback to apply on data
         * @param   string      $cachefile          Caching file path
         * @param   string      $cachetime          Caching time
        **/
        protected function _display($template, \Closure $callback = null, $cachefile = false, $cachetime = 0) {

            $cache = new \Cache\Cache(
                new \Cache\Adapters\FileCache(
                    root(PATH_TMP.'/cache/views')
                )
            );

            if(!$this->user->tracking) {
                $cachefile .= '-notrack';
            }

            if(!$this->settings->environment->debug) {

                // Not modified response (via Cache + ETag)
                if($cachefile && $html = $cache->fetch($key)) {

                    $etag = md5($html);

                    if($this->request->getHeader('ETag') == $etag) {
                        $response = \Message\Response::null(304);
                    }

                }

            }

            // View not cached
            if(!isset($response)) {

                // Get view data

                if(is_closure($callback)) {
                    $data = $this->_initViewData($callback);
                }
                else {
                    $data = $this->_initViewData();
                }

                $html = $this->_view($template, $data);
                $response = \Message\Response::html($html, 200);

                if($cachefile && !$this->settings->environment->debug) {

                    $cache->store($cachefile, $html, $cachetime);

                    $timeleft = $cachetime - (time() - $cache->getLastModifiedTime($cachefile));
                    $response->setCacheHeaders($timeleft, true);
                    $response->setCacheEtag(md5($html), $cache->getLastModifiedTime($cachefile));

                }

            }


            return $response;

        }


        /**
         * Generate a specific view
         * @param   string      $template       Template name
         * @param   array       $data           Data to populate in the view
        **/
        protected function _view($template, array $data = null) {

            $locale = $this->translations;
            $commonMark = new \League\CommonMark\CommonMarkConverter();
            $typofixer = new \JoliTypo\Fixer(
                $this->settings->services->jolitypo->rules->{$this->user->locale}
            );
            $typofixer->setLocale($this->user->locale);

            $view = new \View\Engine(APPLICATION_ROOT.PATH_TEMPLATES.'/application');


            // Assets path helper
            $view->addHelper('asset', function($type, $path) {
                $router = $this->services->get('router');
                $route = $router->getRoute('Media\Media->asset');
                //return $route->getUrl(['type'=> $type, 'path'=>$path]);
                return '/'.$type.'/'.$path;
            });

            // I18N translator helper
            $view->addHelper('i18n', function($message, array $data = null, $context = 'default') use($locale, $commonMark, $typofixer) {
                $translation = $locale->translate($message, $data);
                $translation = $commonMark->convertToHtml($translation);
                $translation = $typofixer->fixString($translation);
                return $translation;
            });


            $view->addHelper('typofix', function($string) use($typofixer) {
                return $typofixer->fixString($string);
            });

            $html = $view->render($template, $data);

            // Minify HTML
            if(!$this->settings->environment->debug) {
                
                /*
                $html =  preg_replace(array(
                    '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                    '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                    '/(\s)+/isU'       // shorten multiple whitespace sequences
                ), array(
                    '>',
                    '<',
                    '\\1'
                ), $html);
                */

                $search = array('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '/\n/','/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
                $replace = array(' ', ' ','>','<','\\1');

                $html = preg_replace($search, $replace, $html);

            }

            return $html;

        }


        /**
         * Retrieve user farovite locale
         * @param   string   $default        Specify the default alternative locale value
         * @return  string                   Return the favorite locale or the default one (the first in the list)
        **/
        public function _getUserLocale($default = null) {

            $locales = $this->settings->application->locales;

            // Formate locales for a better match
            $formated = array_map(function($l) {
                return mb_strtolower(str_replace('_', '-', $l));
            }, $locales);

            // Analyse accepted languages
            $matching = array();
            $accepted = $this->request->getAcceptedLanguages();
            foreach($accepted as $lang) {

                $lang       = trim($lang);
                $quality    = 1;

                // Retrieve language quality (if available)
                if(($qpos = mb_strpos($lang, $prefix = ';q=')) !== false) {

                    $quality = mb_substr($lang, $qpos + mb_strlen($prefix));
                    $lang = mb_substr($lang, 0, $qpos); // Remove language quality string

                }

                // Matching locale
                if(($lpos = array_search(mb_strtolower($lang), $formated)) !== false ) {

                    $code = $locales[$lpos];

                    if(isset($matching[$code])) {
                        $quality = ($matching[$code]['quality'] > $quality ? $matching[$code]['quality'] : $quality);
                    }

                    $matching[$code] = $quality;

                    continue; // Move to the next value

                }

                // Matching language
                foreach($formated as $lpos => $locale) {

                    if(mb_substr($locale , 0, mb_strlen($lang)) == $lang) {

                        $code = $locales[$lpos];

                        if(isset($matching[$code])) {
                            $quality = ($matching[$code]['quality'] > $quality ? $matching[$code]['quality'] : $quality);
                        }

                        if(!isset($matching[$code])) {
                            $pos  = array_search($code, $locales);
                            $matching[$code] = $quality;
                        }

                    }

                }

            }


            // Default value
            if(empty($matching))
                return  (empty($default) ? array_shift($locales) : $default);

            // Reorder and get the best choice
            uasort($matching, function($a, $b) use($matching, $locales) {

                if($a == $b) {

                    // Get key code
                    $akey = array_search($a, $matching);
                    $bkey = array_search($b, $matching);

                    // Get position
                    $bpos = array_search($bkey, $locales);
                    $apos = array_search($akey, $locales);


                    // Order by locales settings position
                    return ($apos < $bpos ? 1: -1);

                }

                return ($a < $b ? -1 : 1);

            });

            $matching = array_keys($matching);
            return array_shift($matching);

        }


    }
