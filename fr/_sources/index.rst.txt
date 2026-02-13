Formularium documentation
=========================

`Formularium`_ is a module for Omeka S. It allows administrators to build forms
for public sites.

.. _Formularium: https://github.com/biblibre/omeka-s-module-Formularium

Form submissions are saved and can be viewed from the administration interface.

Submitting a form can also trigger several "actions". There is only one kind of
action at the moment: sending an email.

Goals
-----

The primary goal of this module is to be a viable alternative for
client-specific "contact us"-type forms.

It is also extensible so that other modules can add their own form components
and actions.

Requirements
------------

* Omeka S >= 4.1.0

Quick start
-----------

1. `Add the module to Omeka S <https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s>`__
2. In the administration interface, click on "Formularium" in the navigation menu
3. Create a new form, give it a name and add "components" (form elements)
4. Optionally, add actions
5. Create a new page for your site, and add the "Formularium" block to this
   page. In the block settings, select the form you created.

Features
________

* Create unlimited forms with an unlimited number of components and actions
* Different component types: text, text area, dropdown list, checkbox, antispam
  (recaptcha), file upload, ... (other modules can add their own)
* One action type: send an email (other modules can add their own)
* Form submissions are entirely saved (uploaded files included) so they can be
  viewed later. Deleting a form submission also removes the attached files.
* A flag can be set to mark a form submission as "handled" (whatever that means to you)
* Form submissions can be filtered by form, site, user (if an authenticated
  user has made a submission), and handled status
* Form submissions can be batch deleted and batch edited (batch edit only
  allows to change the handled status)

Comparison with similar modules
-------------------------------

Contact Us
__________

* Contact Us is easier to set up. Formularium is more customizable
* Contact Us has two antispam solutions: simple and recaptcha. Formularium has
  only recaptcha

Table of contents
=================

.. toctree::
   :maxdepth: 2

   forms
   form-components
   form-actions
   form-submissions
