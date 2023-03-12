window.FormValidate = class {

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

    checkSubmit(options, $next, e = false) {
        if (e) e.preventDefault();
        this.constraints = options;
        this.callback = $next;
        this.count = 0;
        this.handleFormSubmit(this.form);
    }

    insertAfter(newNode, referenceNode) {
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    }

    handleFormSubmit(form) {
        var formContents = validate.collectFormValues(form, { trim: true });
        formContents = this.filterValues(formContents);
        var errors = validate(formContents, this.constraints);
        console.log(errors);
        // then we update the form to reflect the results
        this.showErrors(form, errors || {});
        if (!errors) this.showSuccess();
    }

    filterValues(contentsArray) {
        var value;
        for (var i in contentsArray) {
            value = contentsArray[i];
            if (value != '' && typeof value == 'string' && value != null) {
                contentsArray[i] = value.replace(/\s/gm, ' ').replace(/\s\s+/gm, ' ');
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
            var inputError = document.querySelector('[id="validator-sp----' + inputId + '"]');
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
                console.log(text);
                document.getElementById('ajaxLoading').style.display = 'none';
                this.loadingAjax = false;
                document.body.classList.remove('h-screen', 'overflow-y-hidden');
                if (text.errors && Object.keys(text.errors).length) {
                    setTimeout(() => {
                        this.showErrors(this.form, text.errors);
                        // alert(text.errors[Object.keys(text.errors)[0]][0]);
                    }, 10);
                    return false;
                }
                else if (text.status && text.status.trim() == 'success') {
                    window.location = text.redirect;
                }
            })
            .catch(e => {
                console.log(e);
                document.getElementById('ajaxLoading').style.display = 'none';
                this.loadingAjax = false;
                document.body.classList.remove('h-screen', 'overflow-y-hidden');
                setTimeout(() => {
                    alert('There is an error. Please retry again!');
                }, 10);
            });
    }

    resetFormGroup(input) {
        input.classList.remove('focus:ring-red-500', 'focus:border-red-500');
        input.classList.add('focus:ring-blue-700', 'focus:border-blue-700');
    }

    addError(input, error) {
        var inputId = input.getAttribute('name') || this.count;
        var inputError = document.querySelector('[id="validator-sp----' + inputId + '"]');
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
            input.closest('.form-wrapper,.form-group').append(newDiv);
        } else {
            inputError.querySelector('.error-message').innerHTML = error;
            inputError.style.display = 'flex';
        }
    }

}
window.addEventListener('load', function () {

    validate.extend(validate.validators.datetime, {
        // The value is guaranteed not to be null or undefined but otherwise it
        // could be anything.
        parse: function (value, options) {
            return +moment.utc(value);
        },
        // Input is a unix timestamp
        format: function (value, options) {
            var format = options.dateOnly ? "DD-MMM-YYYY" : "YYYY-MM-DD hh:mm:ss";
            return moment.utc(value).format(format);
        }
    });

    validate.validators.requiredIf = function (value, options, key, attributes, globals) {
        if (options.check(attributes) && (value == null || value == '' || typeof value === 'undefined' || (typeof value == 'string' && value.trim() == ''))) {
            return '^This field is required!';
        }
    };
});

window.setRules = () => {
    var start = moment(window.dob_start, 'MM-DD-YYYY');
    var end = moment(window.dob_end, 'MM-DD-YYYY');
    var rules = {
        email: {
            email: true,
        },
        dob: {
            datetime: {
                dateOnly: true,
                earliest: start.utc(),
                latest: end.utc().add(1, 'days'),
                message: `^Date of Birth must be between ${start.format('DD-MMM-YYYY')} and ${end.format('DD-MMM-YYYY')}`
            }
        },
        examcentre: {
            inclusion: {
                within: window.examcentres.map((a) => { return a.id }),
                message: "^Please Select Any Option!"
            }
        },
        "postalcode": {
            numericality: {
                onlyInteger: true,
                greaterThanOrEqualTo: 100000,
                lessThanOrEqualTo: 999999,
                message: "^Please enter a valid postal code."
            }
        },
        "mobile": {
            numericality: {
                onlyInteger: true,
                greaterThanOrEqualTo: 1000000000,
                lessThanOrEqualTo: 9999999999,
                message: "^Please enter a valid 10 digit mobile no. without code."
            }
        },
        "mobile2": {
            numericality: {
                onlyInteger: true,
                greaterThanOrEqualTo: 1000000000,
                lessThanOrEqualTo: 9999999999,
                message: "^Please enter a valid 10 digit mobile no. without code."
            }
        },
        "makthab_years": {
            requiredIf: {
                check: (a) => { return a.makthab == 'Yes'; }
            },
            numericality: {
                onlyInteger: true,
                greaterThanOrEqualTo: 1,
                lessThanOrEqualTo: 8,
                message: "^Please enter a valid number."
            }
        }
    };
    if (window.selectable_max > 0) {
        rules['admission_options[]'] = {
            // minimum 1 and maximum 3
            length: {
                minimum: window.selectable_min,
                maximum: window.selectable_max,
                message: `^Minimum ${window.selectable_min} and Maximum ${window.selectable_max} options are required.`
            },
            presence: {
                allowEmpty: false,
                message: `^Minimum ${window.selectable_min} and Maximum ${window.selectable_max} options are required.`
            }
        };
    }
    var presence = {
        presence: {
            allowEmpty: false,
            message: '^This field is required!'
        }
    };
    try {
        document.querySelector('#application_form').querySelectorAll('input:not([type="hidden"]), textarea, select').forEach(i => {
            if (i.name == 'tc' || !i.name) {
                return false;
            }
            if (i.name == 'makthab_years') {
                rules[i.name] = {
                    ...rules[i.name]
                };
            } else {
                rules[(i.name)] = {
                    ...rules[i.name],
                    ...presence
                };
            }
        });
        console.log(rules);
        var validated = new FormValidate('#application_form', rules);
    } catch (e) {

    }
}

window.app = () => {
    return {
        dateError: false,
        date: 'Not Selected',
        image: false,
        bc: false,
        bcPdf: false,
        tcPdf: false,
        tc: false,
        village: '',
        po: '',
        ps: '',
        makthab: '',
        examcentres: window.examcentres,
        institutions: window.institutions,
        selected_options: [],
        selected_items() {
            return this.selected_options.map(id => this.institutions.find(i => i.id == id));
        },
        remove_option(id) {
            this.selected_options = this.selected_options.filter(i => i != id);
        },
        add_option(id) {
            if (this.selected_options.indexOf(id) == -1 && this.institutions.find(i => i.id == id) && !isNaN(id)) {
                this.selected_options.push(Number(id));
            }
            document.querySelector('#admission_options').selectedIndex = 0;
        },
        filtered_institutions() {
            return this.institutions.filter(i => this.selected_options.indexOf(i.id) == -1);
        },
        isValidDate(d) {
            d = moment(d, 'DD/MM/YYYY', true).subtract(1, 'days').startOf('day');
            console.log(d._d, moment()._d)
            return d.isAfter(moment(), 'd');
        },
        checkDate(e) {

            this.dateError = true;

            var start = moment(window.dob_start, 'MM-DD-YYYY');
            var end = moment(window.dob_end, 'MM-DD-YYYY');
            var date = e.target.value;

            if (moment(date).isValid()) {
                this.date = moment(date).format('DD-MMM-YYYY');
            } else
                this.date = 'Not Selected';

            this.dateError = !moment(date).isBetween(start, end, undefined, '[]'); //true
        },
        slicer(e) {
            if (e.target.value * 1 > e.target.max * 1) {
                e.target.value = e.target.value.slice(0, e.target.max.length);
            }
        },
        imageChange(e, obj) {
            this[obj] = false;
            if (obj != 'image')
                this[obj + 'Pdf'] = false;
            var file = e.target.files[0];
            var error = false;
            var fileTypes = {
                image: ['image/jpg', 'image/jpeg', 'image/png'],
                bc: ['image/jpg', 'image/jpeg', 'image/png', 'application/pdf'],
                tc: ['image/jpg', 'image/jpeg', 'image/png', 'application/pdf']
            };
            var names = {
                image: 'Image',
                bc: 'Birth Certificate',
                tc: 'Transfer Certificate'
            };
            var errors = {
                image: 'jpg, jpeg, or png',
                bc: 'pdf, jpg, jpeg, or png',
                tc: 'pdf, jpg, jpeg, or png'
            };
            var sizes = {
                image: 512,
                bc: 1024,
                tc: 1024
            };
            if (!fileTypes[obj].includes(file.type))
                error = names[obj] + ' must be ' + errors[obj] + ' format!';
            else if (file.size > sizes[obj] * 1024)
                error = names[obj] + ' maximum size is ' + sizes[obj] + ' kb!';
            if (error) {
                alert(error);
                e.target.value = '';
                return false;
            }
            let fileReader = new FileReader();
            fileReader.onload = (e) => {
                let fileURL = fileReader.result;
                this[obj] = fileURL;
                if (obj != 'image') {
                    if (file.type == 'application/pdf') {
                        this[obj] = false;
                        this[obj + 'Pdf'] = fileURL;
                    }
                } else {
                    localStorage.setItem('dfdfdfImageSetup', btoa(fileURL));
                    console.log(btoa(fileURL));
                }
            };
            fileReader.readAsDataURL(file);
        }
    };
}
