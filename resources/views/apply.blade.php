@extends('layouts.guest')
@section('main')
@include('layouts.navigation')
<x-alert />
<!-- Validation Errors -->
<x-auth-validation-errors class="px-4 mb-4 text-center" :errors="$errors" />

<style type="text/css">
    textarea,
    input:not([type="email"]),
    select {
        text-transform: uppercase;
    }
</style>

@include('front')

<div x-data="window.app()" x-cloak x-init="$nextTick(()=> { window.setRules(); })"
    class="block py-6 mx-auto text-black max-w-7xl sm:px-6 lg:px-8">
    @csrf
    <div class="hidden sm:block" aria-hidden="true">
        <div class="py-5">
            <div class="border-t border-gray-200"></div>
        </div>
    </div>

    <form enctype="multipart/form-data" id="application_form" action="" method="POST">
        @csrf
        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Details</h3>
                        <p class="mt-1 text-sm text-gray-600">Please provide correct information as per documents to
                            avoid mismatches.</p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="overflow-hidden shadow sm:rounded-md">
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 form-wrapper">
                                    <label for="name" class="label">Applicant's
                                        Name</label>
                                    <input type="text" name="name" id="name" autocomplete="name" class="input">
                                </div>

                                <div class="col-span-6 form-wrapper">
                                    <label class="label"> Photo </label>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-block w-16 h-16 overflow-hidden bg-gray-100 rounded-full">
                                            <svg x-show="!image" class="w-full h-full text-gray-300" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <img :src="image" x-show="image" class="object-cover w-full h-full">
                                        </span>
                                        <label for="image"
                                            class="px-3 py-2 ml-5 text-sm font-medium leading-4 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <input type="file" id="image" name="image"
                                                accept="image/jpg, image/jpeg, image/png" class="hidden"
                                                @change="imageChange($event, 'image')" />
                                            <span x-text="image ? 'Change' : 'Select Photo'"></span>
                                        </label>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Passport Size Photo - Max size 512</p>
                                </div>

                                <div class="col-span-6 sm:col-span-4 form-wrapper">
                                    <label for="dob" class="label">Date of
                                        Birth</label>
                                    <input type="date" name="dob" id="dob" autocomplete="dob" class="input"
                                        min="{{ settings('dob_starting_at', null, 'Y-m-d') }}"
                                        max="{{ settings('dob_ending_at', null, 'Y-m-d') }}" pattern="\d{4}-\d{2}-\d{2}"
                                        @change="checkDate($event)" @input="checkDate($event)">
                                    <p class="my-2 text-sm text-green-600" x-text="date"></p>
                                    <p :class="{ 'text-gray-500': !dateError, 'text-red-500': dateError }"
                                        class="mt-2 text-xs">
                                        <b x-show="dateError">*</b> Must be between {{ settings('dob_starting_at', null,
                                        'd-M-Y') }} and {{ settings('dob_ending_at', null, 'd-M-Y') }}
                                    </p>
                                </div>

                                <div class="col-span-6 form-wrapper">
                                    <label for="guardian" class="label">Guardian's
                                        Name</label>
                                    <input type="text" name="guardian" id="guardian" autocomplete="guardian"
                                        class="input">
                                </div>

                                <div class="col-span-6 form-wrapper">
                                    <label class="label"> Birth Certificate </label>
                                    <div
                                        class="flex justify-center px-6 pt-5 pb-6 mt-1 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg x-show="!bc && !bcPdf" class="w-12 h-12 mx-auto text-gray-400"
                                                stroke="currentColor" fill="none" viewBox="0 0 48 48"
                                                aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <img :src="bc" x-show="bc" class="block w-64 h-auto mx-auto mb-4">
                                            <iframe x-show="bcPdf" :src="bcPdf ? bcPdf + '#toolbar=0' : false"
                                                type="application/pdf" title="BC Pdf"
                                                class="block w-64 h-auto mx-auto mb-4 overflow-y-visible"
                                                style="min-height: 22rem;">
                                                <a target="_blank" class="text-blue-500" :href="bcPdf">View selected
                                                    PDF</a>
                                            </iframe>
                                            <div class="flex items-center justify-center text-sm text-gray-600">
                                                <label for="bc"
                                                    class="relative font-medium text-blue-600 bg-white rounded-md cursor-pointer hover:text-blue-500 focus-within:outline-none">
                                                    <span
                                                        x-text="bc || bcPdf ? 'Change the File' : 'Upload a file'"></span>
                                                    <input @change="imageChange($event, 'bc')"
                                                        accept=".pdf, image/jpg, image/jpeg, image/png" id="bc"
                                                        name="bc" type="file" class="sr-only">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PDF, PNG, JPG, JPEG up to 1MB</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-6 form-wrapper">
                                    <label class="label"> School Certificate / Mark
                                        Sheet / Aadhar Card (Required if Present*) </label>
                                    <div
                                        class="flex justify-center px-6 pt-5 pb-6 mt-1 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg x-show="!tc && !tcPdf" class="w-12 h-12 mx-auto text-gray-400"
                                                stroke="currentColor" fill="none" viewBox="0 0 48 48"
                                                aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <img :src="tc" x-show="tc" class="block w-64 h-auto mx-auto mb-4">
                                            <iframe x-show="tcPdf" :src="tcPdf ? tcPdf + '#toolbar=0' : false"
                                                type="application/pdf" title="TC Pdf"
                                                class="block w-64 h-auto mx-auto mb-4 overflow-y-visible"
                                                style="min-height: 22rem;">
                                                <a target="_blank" class="text-blue-500" :href="tcPdf">View selected
                                                    PDF</a>
                                            </iframe>
                                            <div class="flex items-center justify-center text-sm text-gray-600">
                                                <label for="tc"
                                                    class="relative font-medium text-blue-600 bg-white rounded-md cursor-pointer hover:text-blue-500 focus-within:outline-none">
                                                    <span
                                                        x-text="tc || tcPdf ? 'Change the File' : 'Upload a file'"></span>
                                                    <input @change="imageChange($event, 'tc')"
                                                        accept=".pdf, image/jpg, image/jpeg, image/png" id="tc"
                                                        name="tc" type="file" class="sr-only">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PDF, PNG, JPG, JPEG up to 1MB</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Communication Details</h3>
                        <p class="mt-1 text-sm text-gray-600">Enter your correct address and contact numbers.</p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="overflow-hidden shadow sm:rounded-md">
                        <div class="px-4 py-5 space-y-6 bg-white sm:p-6">
                            <div class="grid grid-cols-6 gap-6">

                                <input
                                    :value="village + ( village.length && (po.length || ps.length) ? ', ' : '' ) + ( po.length ? 'PO ' : '') + po + ( po.length && ps.length ? ', ' : '') + ( ps.length ? 'PS ' : '') + ps"
                                    type="hidden" name="address" id="address" autocomplete="address">

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="village" class="label">Village</label>
                                    <input type="text" x-model="village" name="village" id="village"
                                        autocomplete="village" class="input">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="po" class="label">Post Office</label>
                                    <input type="text" x-model="po" name="po" id="po" autocomplete="po" class="input">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="ps" class="label">PS</label>
                                    <input type="text" x-model="ps" name="ps" id="ps" autocomplete="ps" class="input">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="city" class="label">District</label>
                                    <input type="text" name="city" id="city" autocomplete="address-level2"
                                        class="input">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="state" class="label">State</label>
                                    <input type="text" name="state" id="state" autocomplete="address-level1"
                                        class="input">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 form-wrapper">
                                    <label for="postalcode" class="label">PIN
                                        code</label>
                                    <input type="number" name="postalcode" id="postalcode" autocomplete="postalcode"
                                        class="input" min="100000" max="999999" @input="slicer($event)"
                                        @change="slicer($event)">
                                </div>

                                <div class="col-span-6 sm:col-span-3 form-wrapper">
                                    <label for="mobile" class="label">Mobile No. (
                                        Whatsapp ) without code</label>
                                    <input type="number" name="mobile" id="mobile" autocomplete="mobile" class="input"
                                        min="1000000000" max="9999999999" @input="slicer($event)"
                                        @change="slicer($event)">
                                </div>

                                <div class="col-span-6 sm:col-span-3 form-wrapper">
                                    <label for="mobile2" class="label">Alternative
                                        Mobile No.</label>
                                    <input type="number" name="mobile2" id="mobile2" autocomplete="mobile2"
                                        class="input" min="1000000000" max="9999999999" @input="slicer($event)"
                                        @change="slicer($event)">
                                </div>

                                <div class="col-span-6 sm:col-span-4 form-wrapper">
                                    <label for="email" class="label">Email</label>
                                    <input type="email" name="email" id="email" autocomplete="email" class="input">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Additional Details</h3>
                        <p class="mt-1 text-sm text-gray-600">Select most comfortable exam centre and date for the
                            student.</p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="#" method="POST">
                        <div class="overflow-hidden shadow sm:rounded-md">
                            <div class="px-4 py-5 space-y-6 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">

                                    <fieldset class="col-span-6">
                                        <div>
                                            <legend class="text-base font-medium text-gray-900">Exam Centre</legend>
                                            <p class="text-sm text-gray-500">Select any exam center to appear for the
                                                interview on the <b>specific date</b>.</p>
                                        </div>
                                        <div class="mt-4 space-y-4 form-wrapper">
                                            <template x-for="(centre, c) in examcentres">
                                                <div class="flex items-start col-span-6">
                                                    <div class="flex items-center h-5">
                                                        <input :id="'examcentre'+c" name="exam_centre" type="radio"
                                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500"
                                                            :value="centre.id">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label :for="'examcentre'+c" class="font-medium text-gray-700"
                                                            x-text="centre.name"></label>
                                                        <p class="font-bold text-gray-500"
                                                            x-text="moment(centre.date_time).format('DD MMM YYYY hh:mm A')">
                                                        </p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                    </fieldset>

                                    <div class="col-span-6 py-2">
                                        <div class="border-t border-gray-200"></div>
                                    </div>

                                    @if ($settings->selectable_max > 0)
                                    <fieldset class="col-span-6">
                                        <div>
                                            <legend class="text-base font-medium text-gray-900">Admission Options
                                            </legend>
                                            <p class="text-sm text-gray-500">Choose Upto {{
                                                settings('selectable_max') }} Admission Options From the
                                                institutions below.</p>
                                        </div>
                                        <div class="mt-4 form-wrapper">
                                            <template x-for="(opt, c) in selected_items()" :key="opt.id">
                                                <div
                                                    class="px-3 py-1.5 bg-green-100 rounded my-2 shadow-sm w-max text-md">
                                                    <input type="text" class="hidden" name="admission_options[]"
                                                        :value="opt.id">
                                                    <span x-text="'Option ' + (c + 1) + ' - ' + opt.name"></span>
                                                    <button type="button" @click="remove_option(opt.id)"
                                                        class="ml-4 text-lg text-red-700 hover:text-red-800">x</button>
                                                </div>
                                            </template>
                                            <template x-if="!selected_items().length">
                                                <input type="text" class="hidden" name="admission_options[]">
                                            </template>
                                            <label for="admission_options" class="label">Admission Options</label>
                                            <select @change="add_option($event.target.value)" name=""
                                                id="admission_options" class="input">
                                                <option value="" disabled selected>--- Add Option ---</option>
                                                <template x-for="(option, o) in filtered_institutions()"
                                                    :key="option.id">
                                                    <option :value="option.id" x-text="option.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                    </fieldset>
                                    @endif

                                    <div class="col-span-6 py-2">
                                        <div class="border-t border-gray-200"></div>
                                    </div>

                                    <fieldset class="col-span-6">
                                        <div>
                                            <legend class="text-base font-medium text-gray-900">Did the student study in
                                                Madrassa or Maktab?</legend>
                                            <p class="text-sm text-gray-500">Select any option.</p>
                                        </div>
                                        <div class="mt-4 space-y-4 form-wrapper">
                                            <template x-for="(option, o) in ['Yes', 'No']">
                                                <div class="flex items-start col-span-6">
                                                    <div class="flex items-center h-5">
                                                        <input x-model="makthab" :id="'makthab'+o" name="makthab"
                                                            type="radio" :value="option"
                                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label :for="'makthab'+o" class="font-medium text-gray-700"
                                                            x-text="option"></label>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                    </fieldset>

                                    <div class="col-span-6 sm:col-span-3 form-wrapper" x-show="makthab == 'Yes'">
                                        <label for="makthab_years" class="label">If
                                            yes, how many years?</label>
                                        <input type="number" name="makthab_years" id="makthab_years"
                                            autocomplete="makthab_years" class="input">
                                    </div>

                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Declaration</h3>
                        <p class="mt-1 text-sm text-gray-600">Please give correct and accurate information.</p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="overflow-hidden shadow sm:rounded-md">
                        <div class="px-4 py-5 bg-white sm:p-6 form-wrapper">
                            <div class="grid grid-cols-6 gap-6">

                                <div class="flex items-start col-span-6">
                                    <div class="flex items-center h-5">
                                        <input id="declare" name="declare" type="checkbox" value="yes"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="declare" class="font-medium text-gray-700">I hereby declare that the
                                            above mentioned information are correct and true according to the best of my
                                            knowledge.</label>
                                        <div class="text-red-500">Please Print the Hallticket, Application Form and
                                            Submitted Documents after completing the application. All those should be
                                            submitted on Date of Admission Test.</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="px-4 py-3 text-center bg-gray-50 sm:px-6">
                            <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Submit
                                Application</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
    integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    window.examcentres = @json($examcentres);
    window.institutions = @json($institutions);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
@endsection