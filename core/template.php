<?php

    class Template extends Response {
        private static $callback = null;
        private static $cache = false;
        private static $php = false;
        private static $unsafe = array(
            'exec', 'system', 'popen', 'shell_exec', 
            'include', 'require', 'include_once', 'require_once'
        );
        
        
        public function __construct($callback, $cache = false, $php = false) {
            self::$callback = $callback;
            self::$cache = $cache;
            self::$php = $php;
        }
        
        
        /**
         * Generate template output
        **/
        public static function generate($buffer, $template){
            if(!empty(self::$callback)):
                if(is_callable(self::$callback)):
                    $parser = self::$callback;
                    return $parser($buffer, $template);
                else:
                    return self::parse($buffer);
                endif;
            else:
                return $buffer;
            endif;
        }
        
        /**
         * Default template parser
        **/
        protected static function parse($buffer) {
            // Includes
            $buffer = self::comments($buffer); // Comments
            $buffer = self::comments(self::zones($buffer)); // Includes
            $buffer = self::loops($buffer); // Loops
            $buffer = self::jumps($buffer); // Jumps (for)
            $buffer = self::conditions($buffer); // Conditions
            $buffer = self::variables($buffer); // Variables
            $buffer = self::locales($buffer); // Locales
            $buffer = self::constants($buffer); // Constants
            $buffer = self::replacements($buffer); // Replacements
            
            return $buffer;
        }
        
        /**
         * Comments parser
         * {* this text will not be viewed *}
        **/
        protected static function comments($buffer) {
            return preg_replace('#\{\*(.+)\*\}#isU', null, $buffer);
        }
        
        /**
         * Include parser
         * {inc "path/to/file"}
        **/
        protected static function zones($buffer) {
             return preg_replace_callback('#\{zone "(.+)"\}#isU', function($matches) {
                // Bug with parent::$base /!\
                //$path = str_replace('~', parent::$base, $matches[1]);
                $path = PATH_TEMPLATES . '/' . $matches[1] . '.php';
                
                if(file_exists(root($path))):
                    ob_start();
                    include(root($path));
                    $include = ob_get_contents();
                    ob_end_clean();
                    return $include;
                else:
                    Console::error("Template parser : can't include zone '$path'");
                endif;
                 
            }, $buffer);
        }
        
        /**
         * Loops parser
         *  {loop $array} {$key} : {$value} {/loop}
        **/
        protected static function loops($buffer) {
            return preg_replace_callback('#\{loop \$([a-z0-9\._]+)\}(.+)\{/loop}#isU', function($matches) {
                $path = implode("']['", explode('.', $matches[1]));
                @eval('$var = &Response::$data[\''.$path.'\'];');
                $output = null;
                
                if(!empty($var) && is_array($var)):
                    foreach($var as $key => $value) {
                        $new = str_replace('{$key}', $key, $matches[2]);
                        
                        if(is_array($value)):
                            $new = preg_replace_callback('#\{\$value(\..+)\}#isU', function($matches) {
                                $path = implode("']['", explode('.', $matches[1]));
                                @eval('$var = Response::$data[\''.$path.'\'];');
                                return $var;
                            }, $new);
                            
                        else:
                            $new = str_replace('{$value}', $value, $new);
                        endif;
                        
                        $output .= $new;
                    }
                    return $output;
                else:
                    Console::warning("Template parser : variable $var can't be used in a loop (not an array)");
                endif;
            }, $buffer);
        }
        
        
        /**
         * Jumps (for) parser
         * {jump 12 => 1234 / 5} {$step} / {$i} {/jump}
         * {jump 0 => 123 [/ 1]} {$step} {$i} {/jump}
        **/
        protected static function jumps($buffer) {
            return preg_replace_callback('#\{jump ([0-9]+) ?=> ?([0-9]+) ?(/ ?([0-9]+))?\}(.+)\{/jump}#isU', function($matches) {
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
                        $output .= str_replace(array('{$i}', '{$step}'), $i, $matches[5]);
                        @eval("\$i = \$i +  $jump;");
                    endfor;
                endif;
                
                return $output;
                
            }, $buffer);
        }
        
        /**
         * Data replacement parsers
        **/
        protected static function replacements($buffer) {
            return preg_replace_callback('#\{"(.+)" \$([a-z0-9\._]+)\}#isU', function($matches) {
                $path = implode("']['", explode('.', $matches[2]));
                @eval('$array = &Response::$data[\''.$path.'\'];');
                if(!empty($array) && is_array($array)):
                    $output = $matches[1];
                    foreach($array as $index => $value) {
                        $output = str_replace(":$index", $value, $output);
                    }
                    return $output;
                else:
                    Console::warning("Template parser : Variable '\$".$matches[2]."' is not an array");
                endif;
                
            }, $buffer);
        }
        
        
        /**
         * Variables parser
         * {$variable}
        **/
        protected static function variables($buffer) {
            return preg_replace_callback('#\{\$([a-z0-9\._]+)\}#isU', function($matches) {
                $path = implode("']['", explode('.', $matches[1]));
                @eval('$var = &Response::$data[\''.$path.'\'];');
                
                if(!empty($var) && is_string($var))
                    return $var;
                else
                    Console::warning("Template parser : variable ".$matches[1]." not found");
            }, $buffer);
        }
        
        
        /**
         * Constants parser
         * {#CONSTANT}
        **/
        protected static function constants($buffer) {
            return preg_replace_callback('#\{\#([a-z0-9\\_]+)\}#isU', function($matches) {
                if(defined($matches[1]))
                    return constant($matches[1]);
                else
                    Console::warning('Template parser : constant '.$matches[1].' not defined');
            }, $buffer);
        }
        
        
        /**
         * Locales parser
         * {@file:array.item}
         * {@file:array.item $data}
        **/
        protected static function locales($buffer) {
            return preg_replace_callback('#\{@(.+)( \$([a-z0-9\._]+))?\}#isU', function($matches) {
                @eval('$locale = $matches[1];');
                if(!empty($matches[3])):
                    $path = implode("']['", explode('.', $matches[3]));
                    @eval('$var = &Response::$data[\''.$path.'\'];');
                    return (is_array($var) ? Locales::_e($locale, $var) : null);
                else:
                    return Locales::_e($locale);
                endif;
            }, $buffer);
        }
        
        
        
        /**
         * Conditions parser;
         * {if $conditions} {/endif}
         * {if $conditions} {/if}
        **/
        protected static function conditions($buffer) {
            $variables = function($string) {
                return self::variables($string, false);
            };
            
            $constants = function($string) {
                return self::constants($string, false);
            };
            
            return preg_replace_callback('#\{if (.+)\}(.+)\{(/if|endif)\}#isU', function($matches) {
                // [Security] To do : check unsafe functions

                $conditions = preg_replace_callback('#\$([a-z0-9\._]+)#is', function($m) {
                    $path = implode("']['", explode('.', $m[1]));
                    return 'Response::$data[\''.$path.'\']';
                }, $matches[1]);
                
                $conditions = preg_replace_callback('#\#([a-z0-9_]+)#is', function($m) {
                    return 'constant("'.$m[1].'")';
                }, $conditions);
                
                //echo('$assertion = ('.$conditions.');');
                @eval('$assertion = ('.$conditions.');');
                
                if($assertion):
                    return $matches[2];
                endif;
            }, $buffer);
        }
        
    }


    //*
    new Template(function($buffer, $template) {
        return str_replace('Hello', "Good night", $buffer);
    });
    //*/

    new Template(true);

?>