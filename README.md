# Tripleseat Breakdance Form Action
This is a Wordpress plugin that works alongside [Breakdance builder](https://breakdance.com/ref/376/). It takes a Form submission and lets you map fields to feed lead data into [Tripleseat](https://support.tripleseat.com/hc/en-us/articles/205161948-Lead-Form-API-endpoint). 

## Why this exists (as of 3/18/25)
I wrote this plugin for a client who uses Tripleseat for event management. I figured somebody else out there might have a similar setup and need this. I've tested the major form fields and all seems to work. I have yet to test multiple locations and lead sources.

If you have any questions, bugs, etc. please [create a new issue](https://github.com/Nicscott01/tripleseat-breakdance-form-action/issues/new).


### A note to other Breakdance Developers out there
In trying to better handle errors, I ran into an issue where the [Breakdance documentation for form actions api](https://github.com/soflyy/breakdance-developer-docs/blob/master/form-actions/readme.md) incorrectly shows response arrays if you have an error. The array should look like:
```
[
     'type' => 'user-error',
     'message' => 'This message will get returned in the form validation response'
]
```


## Changelog
### v1.0.3
- Edited key for additional_information field that was not transmitting
- Added field for `lead_form_ID`
- Tested form fields and saw results in Tripleseat
- Email opt-in works
