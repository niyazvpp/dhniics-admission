@extends('layouts.guest')
@section('main')

@include('layouts.navigation')
<x-alert />

@include('front')

<div x-data="{}" x-init="window.setRules()" x-cloak class="block py-6 mx-auto text-black max-w-7xl sm:px-6 lg:px-8">

    <div class="px-4 py-4 my-4 text-center bg-white shadow sm:px-6 rounded-xl">
        <h3 class="mb-2 text-2xl font-semibold text-gray-700">Search Application</h3>
        <form method="POST" id="search" action="{{ route('applications') }}">
            @csrf
            <div class="flex flex-col items-center justify-center my-4 space-y-3 form-wrapper">
                <div class="block w-full max-w-xl text-left form-wrapper">
                    <label for="mobile" class="label">Mobile</label>
                    <input required type="number" name="mobile" id="mobile" autocomplete="mobile" class="input">
                </div>
                <div class="block w-full max-w-xl text-left form-wrapper">
                    <label for="dob" class="label">Date of Birth</label>
                    <input required type="date" name="dob" id="dob" autocomplete="dob" class="input">
                </div>
            </div>
            <button type="submit" class="btn-green btn">
                Search
            </button>
        </form>
    </div>

    <div class="my-6 bg-white shadow rounded-xl">
        <table class="w-full table-auto rounded-xl">
            <thead class="">
                <tr class="bg-gray-50 rounded-t-xl">
                    <th class="px-4 py-2 rounded-t-xl" colspan="5">
                        Your Applications List
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $data)
                <tr class="text-center border-t">
                    <td class="px-1 py-2 sm:px-4">{{ $data->ref_no }}</td>
                    <td class="px-1 py-2 sm:px-4">{{ $data->name }}</td>
                    <td class="px-1 py-2 text-center sm:px-4">
                        <div class="flex justify-center">
                            <a target="_blank"
                                class="mx-2 text-blue-500 bg-gray-100 border shadow-none btn hover:bg-gray-200 hover:text-blue-600"
                                href="{{ route('hallticket', ['slug' => $data->slug]) }}">Hall Ticket <i
                                    class="ml-2 fa fa-download"></i></a>
                            <a target="_blank"
                                class="mx-2 text-blue-500 bg-gray-100 border shadow-none btn hover:bg-gray-200 hover:text-blue-600"
                                href="{{ route('applicationPrint', ['slug' => $data->slug]) }}">Application<i
                                    class="ml-2 fa fa-download"></i></a>
                            <a target="_blank"
                                class="mx-2 text-blue-500 bg-gray-100 border shadow-none btn hover:bg-gray-200 hover:text-blue-600"
                                href="{{ route('documents', ['slug' => $data->slug]) }}">Docs <i
                                    class="ml-2 fa fa-download"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $applications->links() }}
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
    integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
<script>
    class FormValidate {

    constructor($form, options, $next = false, $submit = true) {
        this.loadingAjax = false;
      $form = document.querySelector($form);
      $form.setAttribute('novalidate', true);
      this.form = $form;
      this.highlighted = false;
      var $this = this;
      if ($submit) {
        $form.addEventListener("submit", function (ev) {
          $this.checkSubmit(options, $next, ev);
        });
      }
      else {
          this.checkSubmit(options, $next);
      }
    }

    checkSubmit(options, $next, e=false) {
      if(e) e.preventDefault();
        this.constraints = options;
        this.callback = $next;
        this.count = 0;
        this.handleFormSubmit(this.form);
    }

    insertAfter(newNode, referenceNode) {
      referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    }

    handleFormSubmit(form) {
      var formContents = validate.collectFormValues(form, {trim: true});
      formContents = this.filterValues(formContents);
      var errors = validate(formContents, this.constraints);
        // then we update the form to reflect the results
        this.showErrors(form, errors || {});
        if (!errors) this.showSuccess();
    }

    filterValues(contentsArray) {
      var value;
      for (var i in contentsArray) {
        value = contentsArray[i];
        if (value != '' && typeof value == 'string' && value != null) {
          contentsArray[i] = value.replace(/\s/gm,' ').replace(/\s\s+/gm, ' ');
        }
        if (value === false) contentsArray[i] = null;
      }
      return contentsArray;
    }

      showSuccess() {
        if (this.callback)
            this.callback(this.form, this);
        else this.ajaxSubmit(this.form);
      }

      showErrors(form, errors) {
        this.highlighted = false;
        var inputs = form.querySelectorAll("input[name], select[name], textarea[name]");
        for (var i = 0; inputs.length > i; i++) {
          this.showErrorsForInput(inputs[i], errors && errors[inputs[i].getAttribute('name')] || errors[inputs[i].getAttribute('id')]);
        }
        if (this.highlighted)
           this.highlighted.focus();
      }

      showErrorsForInput(input, errors) {
        // First we remove any old messages and resets the classes
        this.resetFormGroup(input);
        // If we have errors
        if (errors) {

          if (!this.highlighted)
            this.highlighted = input;

          this.count++;
          // we first mark the group has having errors
          if (!(input).matches('[type="checkbox"]') && !(input).matches('[type="radio"]')) {
            input.classList.add('focus:ring-red-500', 'focus:border-red-500');
            input.classList.remove('focus:ring-blue-700', 'focus:border-blue-700');
          }

          // then we append all the errors
          //for (var i = 0; i < errors.length; i++) {
            this.addError(input, errors[0]);//errors[i]);
          //}
        } else {
          // otherwise we simply mark it as success
          var inputId = input.getAttribute('name') || this.count;
          var inputError = document.querySelector('[id="validator-sp----' + inputId+ '"]');
          if (inputError) inputError.style.display = 'none';
        }
      }

      ajaxSubmit(form) {
        if (this.loadingAjax) return;
        this.loadingAjax = true;
        document.getElementById('ajaxLoading').style.display = 'block';
        const token = '{{ csrf_token() }}';
        document.body.classList.add('h-screen', 'overflow-y-hidden');
        var formData = new FormData(form);
        fetch(form.action, {
            credentials: "same-origin",
          method: form.method || 'POST',
          headers: {
            'Accept': 'application/json',
            "X-CSRF-Token": token
          },
          body: formData
        })
        .then(response => {
          return response.json();
        })
        .then(text => {
            document.getElementById('ajaxLoading').style.display = 'none';
            this.loadingAjax = false;
          document.body.classList.remove('h-screen', 'overflow-y-hidden');
            if (text.errors && Object.keys(text.errors).length) {
            setTimeout(()=> {
                this.showErrors(this.form, text.errors);
                // alert(text.errors[Object.keys(text.errors)[0]][0]);
            }, 10);
            return false;
            }
            else if (text.status && text.status.trim() == 'success' ) {
                window.location = text.redirect;
            } else {
                window.location = '';
            }
        })
        .catch(e => {
            document.getElementById('ajaxLoading').style.display = 'none';
            this.loadingAjax = false;
            document.body.classList.remove('h-screen', 'overflow-y-hidden');
            window.location = '';
        });
    }

      resetFormGroup(input) {
        input.classList.remove('focus:ring-red-500', 'focus:border-red-500');
        input.classList.add('focus:ring-blue-700', 'focus:border-blue-700');
      }

      addError(input, error) {
        var inputId = input.getAttribute('name') || this.count;
        var inputError = document.querySelector('[id="validator-sp----' + inputId+ '"]');
        if (!inputError) {
            var newDiv = document.createElement('div');
            newDiv.innerHTML = `<div class="flex items-center pt-3" id="validator-sp----${inputId}">
              <div class="mr-1 text-red-500 rounded-full">
              <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" focusable="false" width="16px" height="16px" viewBox="0 0 24 24" xmlns="https://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
              </div>
              <span class="text-sm font-medium text-red-500 error-message">${error}</span>
              </div>`;
          // if ((input).matches('[type="checkbox"]') || (input).matches('[type="radio"]')) {
          //     (input).closest('label').closest('.flex').closest('div').after(newDiv);
          // } else {
          //      (input).after(newDiv);

          // }
          input.closest('.form-wrapper').append(newDiv);
        } else {
          inputError.querySelector('.error-message').innerHTML = error;
          inputError.style.display = 'flex';
        }
      }

    }

    // Before using it we must add the parse and format functions
    // Here is a sample implementation using moment.js
    validate.extend(validate.validators.datetime, {
      // The value is guaranteed not to be null or undefined but otherwise it
      // could be anything.
      parse: function(value, options) {
        return +moment.utc(value);
      },
      // Input is a unix timestamp
      format: function(value, options) {
        var format = options.dateOnly ? "DD-MMM-YYYY" : "YYYY-MM-DD hh:mm:ss";
        return moment.utc(value).format(format);
      }
    });

    validate.validators.requiredIf = function(value, options, key, attributes, globals) {
          if (options.check(attributes) && (value == null || value == '' || typeof value === 'undefined' || (typeof value == 'string' && value.trim() == ''))) {
            return '^This field is required!';
        }
    };

    window.setRules = ()=> {
        var rules = {
            dob: {
                datetime: {
                    dateOnly: true
                }
            },
            mobile: {
                numericality: {
                      message: "^Please enter your mobile Number."
                }
            }
        };
        var presence = {
            presence: {
                allowEmpty: false,
                message: '^This field is required!'
            }
        };
        document.querySelector('#search').querySelectorAll('input:not([type="hidden"]), textarea, select').forEach(i => {

            rules[i.name] = {
                ...rules[i.name],
                ...presence
            };

        });
        var validated = new FormValidate('#search', rules);
    }

</script>

@endsection