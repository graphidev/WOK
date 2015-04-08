# Web Operational Kit

## About the nor huge neither micro framework

**WOK** is a framework build for developers who need some tools without having a factory.  It is not made for huge projects such as web apps. But it contains enought tools to build a CMS in a few days.

The main idea of this framework is to give a base structure (MVC) and some essential tools (such as UTF8 revisited functions, compatibility helpers, ...). You will not find hundreds of configuration files but just one (and maybe an options file too, but that's all !). 

As it's nickname is associated to : WOK is made for cooking, development cooking !

## Features

As I said, no extra-features would be present. But there is some of them that I think every developer could need :

- One-shot configuration and use (yeah, that's a feature !)
- Multi platform (working on Windows and Unix-based systems)
- MVC structure
- URL rewriting
- System environments (Debug, maintenance, production) 
- Default behavior (for static pages)
- Router using HTTP and URI parameters conditions
- Response with file cache system and HTTP caching
- Secure Cookies management (encryption)
- Session tools (also working with cookies)
- Helpers functions
- Multilingual system (using locales)
- Errors listener and log management (for debugging as example)
- Extended exceptions classes
- External libraries which are not required for the framework operations


## Versioning convention
Because WOK is evolving according to requierements, there is no plan about versions (except for huge features i think about). However, versioning convention can be defined as following : MAJOR.MINOR:PATCH-EXTRA


**MAJOR** : Main projects chagements such as a restructuration

**MINOR** : New features, providing backward compatibility

**PATCH** : Security fixes, bug fixes, or optimisation of an existing code

**EXTRA**: Note about the version defining what type of major either minor version it is. It could be :
- prototype : draft of a future version (may be aborted)
- alpha : in development version (fixed principle)
- beta : fixing bugs and security breaks (looking to feedbacks)
- stable : production usable version

Each update of the code has to increment the version number, following the previous versioning convention

## Requirements

Here is the price to use an awesome tool. But don't worry, just some little things :

- Apache v2 server with URL Rewriting module
- PHP v5.3 or higher (but always prefer an higher version)
- Mcrytp library (used for cookies encryption)
- SPL library (that should be present by default)

## Author

My name is Sébastien. I have built this framework in order to answer positive to my customers requests. But I think that sharing tools is better than selling them. So, enjoy !

## Contributors

Feel free to work on it and suggest features ! I will always love to talk about it !

## Licence

WOK is under BSD licence which means ...

All rights reserved to Sébastien ALEXANDRE.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

* Redistributions of source code must retain the above copyright
  notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in the
  documentation and/or other materials provided with the distribution.
* The name of the author may not be used to endorse or promote products
  derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
