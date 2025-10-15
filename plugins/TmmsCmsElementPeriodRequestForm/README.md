# TmmsCmsElementPeriodRequestForm

## An extension for a request form for the CMS for [Shopware 6](https://github.com/shopware/platform).

### Description

A shopware 6 extension for a _request form for the CMS_, which can be placed in a Shopping Experience using the _own CMS block_ of the same name or the _own CMS element_ of the same name.

The following fields can be displayed in the form and, _if necessary, marked as required fields_: 
- salutation
- first name
- last name
- street address
- postal code
- city
- country
- email
- phone number
- comment
- date field
- data protection information
- and ten fields for free inputs (of the type select, input or textarea field).

At the date field, you can also specify a _start_ and _end date_ and enter _dates that should not be selectable_. Furthermore, the _calendar can also be displayed permanently open_ or you can _display 2 months side by side_.

The comment, the date field and the free inputs can also be displayed _above the form_ if necessary.

Among other things, the _title_, an _introductory text_ and a _confirmation text_ can be specified for the form. Furthermore the _form can either be sent by email to an email address_ and / or the _data entered can be saved in the database_. 

Via the new menu item _requests_ under _customer_ you can manage the incoming requests and confirm a period request from a customer so that the requested date can not be longer selected in the date field on the respective page, i.e. in the category, on the landing page or at the product where the form was placed. You can also specify within the extension configuration that the confirmed status of the requests should be ignored so that the requested data is already automatically deactivated.

Requests can also be added manually via the administration module. 

By hiding selected fields, the form can also only be used for general requests, for example without a date selection, so that it can be used as a normal contact form.

### Possible Configurations
- select if the confirmed status of the requests should be ignored
- select if the origin of the requests should be ignored
- select the date format of the calendar
- select if the weeks numbers in the calendar should be shown
- select if the date field in color should be highlighted

### Available snippets for customizing
- tmms.periodRequestForm.salutation.label
- tmms.periodRequestForm.salutation.placeholder
- tmms.periodRequestForm.firstname.label
- tmms.periodRequestForm.firstname.placeholder
- tmms.periodRequestForm.lastname.label
- tmms.periodRequestForm.lastname.placeholder
- tmms.periodRequestForm.street.label
- tmms.periodRequestForm.street.placeholder
- tmms.periodRequestForm.zipcode.label
- tmms.periodRequestForm.zipcode.placeholder
- tmms.periodRequestForm.city.label
- tmms.periodRequestForm.city.placeholder
- tmms.periodRequestForm.country.label
- tmms.periodRequestForm.country.placeholder
- tmms.periodRequestForm.email.label
- tmms.periodRequestForm.email.placeholder
- tmms.periodRequestForm.phone.label
- tmms.periodRequestForm.phone.placeholder
- tmms.periodRequestForm.comment.label
- tmms.periodRequestForm.comment.placeholder
- tmms.periodRequestForm.date.dateLabel
- tmms.periodRequestForm.date.datePlaceholder
- tmms.periodRequestForm.date.dateFormat
- tmms.periodRequestForm.date.showWeeksNumbers
- tmms.periodRequestForm.freeinput.label
- tmms.periodRequestForm.freeinput.placeholder
- tmms.periodRequestForm.origin.productLabel
- tmms.periodRequestForm.origin.navigationLabel
- tmms.periodRequestForm.origin.landingpageLabel
- tmms.periodRequestForm.submitLabel
- tmms.periodRequestForm.inputMaxLength
- tmms.periodRequestForm.textareaMaxLength
- tmms.periodRequestForm.textareaNumberRows
