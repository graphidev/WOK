# Web Operational Kit

## About the humble nor huge neither micro framework

**WOK** is a framework build for developers who need some tools without having a factory.  It is made for quick projets that could be iterative afterwards.

The main idea of this framework is to give an architecture  (MVC) and some essential tools (such as UTF8 revisited functions, compatibility helpers, ...). You will not find hundreds of configuration files but just one (and maybe an options file too, but that's all !).

As it's nickname is associated to : WOK is made for cooking, development cooking !

## Features

As I said, no extra-features would be present. But there is some of them that I think every developer could need :

- MVC architecture
- URL rewriting based
- Helpers as unicode support and utilities functions
- Modular components based, including :
    - Application _(runtime container classes)_
    - Router _(setting, getting and dispatching routes interface)_
    - Message _(both HTTP and CLI input/ouput interfaces)_
    - View _(a template manager and view generator)_
    - Cache _(an interface to use caches systems)_
    - Console _(logger environment)_
    - Locales _(providing a translation interface)_


## Versioning convention

This framework releases versioning are based on the [Semantic versioning 2.0.0](http://semver.org/). This means that this project releases use the **MAJOR**.**MINOR**.**PATCH**-**EXTRA** template where :

- **MAJOR** : represents a huge project change (and incompatibility with previous releases),
- **MINOR** : represents project improvements (retaining backward compatibility)
- **PATCH** : represents fixes and optimisations
- **EXTRA** : is a keyword representing the development state of the release.

**Note** : Each release (except patching ones) has also it's nice name (ex: Hydrogen, Helium, ...).

## Requirements

Here is the price to use an awesome tool. But don't worry, just some little things :

- Apache v2 server with URL Rewriting module (The nginx compatibility is not provided, for now)
- PHP v5.4 or higher (but always prefer an higher version) (Not test in PHP v7)
- Mcrytp library (used for cookies encryption)
- SPL library (that should be present by default)


## Author

My name is Sébastien. I have built this framework in order to answer positive to my customers requests when I was a freelance. But I think that sharing tools is better than selling them. So, please feel free to use, contribute and enjoy it !


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
