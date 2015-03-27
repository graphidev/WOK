<?php

    namespace App\Controllers;

    use \Framework\Core\Request;
    use \Framework\Core\Response;
    use \Framework\Utils\Collection;

    class Users extends \Framework\Core\Controller {


        /**
         * Register a new user
        **/
        public function register() {

            // Get used services
            $request = $this->getService('request');
            $locales = $this->getService('locales', array( $request->language, 'module' ));
            //$response = $this->getService('response', array('view'));

            $response

            $form = new App\Services\Form(new Collection(
                'name'      => (object) array(
                    'type'          => 'text',
                    'label'         =>  $locales->translate('users:form.fields.name.label'),
                    'placeholder'   =>  $locales->translate('users:form.fields.name.placeholder')
                ),
                'email'     => (object) array(
                    'type'          => 'email',
                    'label'         => $locales->translate('users:form.fields.email.label'),
                    'placeholder'   => $locales->translate('users:form.fields.email.placeholder')
                ),
                'password'  => (object) array(
                    'type'          => 'email',
                    'label'         => $locales->translate('users:form.fields.email.label'),
                    'placeholder'   =>
                ),
            ), Request::get('data'));


            $requierements = $form->requierements(array('name', 'email'));
            /*
            $requierements = array_diff(
                (array) $request->parameters,
                array('name', 'email')
            );
            */

            if(!empty( $requierements )) {
                return Response::view('accounts/register', 206)->headers(array(
                    'X-Missing-Post-Data' => implode(',', $requierements);
                ))->assign(array(
                    'fields' => $form->getFields();
                    'error' => true,
                    'requierements' => $requierements
                ));
            }


            try {

                $user = new \App\Models\Users( $this->getService('database') );

                $user->register(array(
                    'name'  => $request->data('name'),
                    'email' => $request->data('email')
                ));
                /*
                $query = $db->prepare('INSERT INTO users VALUES('', :name, :email)');
                $db->exec(array(
                    'name' => $name,
                    'email' => $email,
                ));
                */

                return Response::view('accounts/register', 201)->assign(array(
                    'message' => (object) array(
                        'type' => 'success',
                        'content' => $locales->translate('users:register.success')
                    )
                ));

            }
            catch(\RuntimeException $e) {

                return Response::view('accounts/register', 409)->assign(array(
                    'message' => (object) array(
                        'type' => 'error',
                        'content' => $locales->translate('users:register.error')
                    )
                ));

            }

        }

        /**
         * Edit an existing user
         * @param int   $id     The user id
        **/
        public function editUser($id) {


        }


    }
