# 1.2.6
- Fixed the problem where forms with required fields could be submitted without any input

# 1.2.5
- You can now determine whether the data protection information should be shown even without terms of service or whether an own snippet should be used for the text

# 1.2.4
- There is now a switch "Answered" below "Customers" and "Requests" if the extension is only used to collect requests

# 1.2.3
- The origin, the origin value and the origin id are now also transferred to the hidden fields of the form if the form is displayed in a modal and the link to the modal has the CSS class period-request-form-modal-btn and the attributes data-origin, data-origin-name and data-origin-id with the corresponding values

# 1.2.2
- Empty requests are no longer created when the route to process the form is called directly
- The confirmation text after successfully submitting the form is no longer maintained in the configuration of the form, but in the snippet with the name "tmms.periodRequestForm.confirmationText", where all entered data of the form is available
- The required field check of the date field has been improved
- The form is now sent directly via the form ajax submit function from Shopware 6

# 1.2.1
- You can now set and show up to 10 free inputs

# 1.2.0
- Established the compatibility with Shopware from version 6.6.0.0

# 1.1.7
- Established the compatibility with Shopware from version 6.5.8.2

# 1.1.6
- Established the compatibility with Shopware from version 6.5.8.0

# 1.1.5
- The label of the period or date selection field is now also transferred to the e-mail, insofar as the value "Zeitraum" and "period" have been replaced by {{ periodRequestFormData.labelDate }} in the existing email template

# 1.1.4
- You can now determine whether a period or date selection should be offered
- You can now set the label of the period or date selection field

# 1.1.3
- Minor bug fixes

# 1.1.2
- Fixed the problem that the captcha "Google reCAPTCHA v3" was no longer loaded under Shopware 6.5

# 1.1.1
- If a mail header and footer for the Email template was assigned to the sales channel, this will now be used when sending the data by email

# 1.1.0
- Established the compatibility with Shopware from version 6.5.0.0
- You can now determine whether the data protection information should be shown
- You can now determine whether the data protection information is to be treated as a required field and a checkbox field should be shown
- You can now determine whether the required fields information should be shown
- You can now set the text of the submit button
- It is now also possible to have several captcha methods active at the same time

# 1.0.12
- The field "confirmation text" in the configuration of the CMS element is now also provided as a TinyMCE field, so that its content is delivered as HTML code in the frontend

# 1.0.11
- When data is sent by email, the customer email is now set as the reply address (Reply-To) for the recipients

# 1.0.10
- When sending emails, the AbstractMailService is now used instead of the MailService

# 1.0.9
- You can now determine whether the period request form text should be shown after the successful submission of the form
- You can now set and show up to 4 free inputs

# 1.0.8
- The field type of the comment field and the free input field has been changed to LONGTEXT so that both fields can hold more characters
- All single-line and multi-line input fields now have the maxlength attribute

# 1.0.7
- Established the compatibility with Shopware from version 6.4.14.0

# 1.0.6
- The annotations of the controller have been replaced with Route defaults

# 1.0.5
- The captcha "Google reCAPTCHA v3", which is triggered when the privacy checkbox is clicked, is now possible at the request form

# 1.0.4
- You can now also send the email to several email receivers, which are separated by commas
- You can now also send the email to the requester

# 1.0.3
- Established the compatibility with Shopware from version 6.4.10.0

# 1.0.2
- You can now select the type of the free input, for example "select field", "input field" or "textarea field"
- You can now also manually set the origin, the origin value and the origin id of the request in the form if necessary
- The code specifically for the validation of the captcha "basic captcha" has been optimized

# 1.0.1
- The captcha "basic captcha" is now possible at the request form

# 1.0.0
- Initial release of the app
