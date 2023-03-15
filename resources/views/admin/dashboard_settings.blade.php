<h3 class="mb-2 text-2xl font-semibold text-gray-700">Settings</h3>
<x-auth-validation-errors class="mb-4" :errors="$errors" />
<form method="POST" action="{{ route('settings') }}" enctype="multipart/form-data">
    @csrf
    <div class="flex items-center justify-center my-4 form-wrapper">
        <div class="w-full grid-cols-4 sm:grid">
            <div class="col-span-4">
                <h3 class="mb-3 ml-2 text-lg font-semibold text-left text-gray-700">General Settings</h3>
            </div>

            <div class="mx-2 text-left">
                <label for="header" class="label">Site Header:</label>
                <input required type="text" id="header" name="header" value="{{ $settings->header ?? '' }}"
                    autocomplete="header" placeholder="Please Use Shorter..." class="input">
            </div>

            <div class="col-span-2 mx-2 text-left">
                <label for="site_name" class="label">Site Title:</label>
                <input required type="text" id="site_name" name="site_name" value="{{ $settings->site_name ?? '' }}"
                    autocomplete="site_name" placeholder="What you view as tab title" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="academic_year" class="label">Academic Year:</label>
                <input required type="text" id="academic_year" name="academic_year"
                    value="{{ $settings->academic_year ?? '' }}" autocomplete="academic_year"
                    placeholder="{{ date('Y') }} - {{ date('Y') - 1999 }}" class="input">
            </div>
            <div class="col-span-2 mx-2 text-left">
                <label for="header_first_line" class="label">Header First Line:</label>
                <input required type="text" id="header_first_line" name="header_first_line"
                    value="{{ $settings->header_first_line ?? '' }}" autocomplete="header_first_line"
                    placeholder="DARUL HUDA ISLAMIC UNIVERSITY KERALA" class="input">
            </div>
            <div class="col-span-2 mx-2 text-left">
                <label for="header_second_line" class="label">Header Second Line:</label>
                <input required type="text" id="header_second_line" name="header_second_line"
                    value="{{ $settings->header_second_line ?? '' }}" autocomplete="header_second_line"
                    placeholder="NIICS CAMPUS" class="input">
            </div>
            <div class="col-span-2 mx-2 text-left">
                <label for="header_third_line" class="label">Address and Contact:</label>
                <textarea required type="text" id="header_third_line" name="address_and_contact"
                    autocomplete="header_third_line" placeholder="3rd and 4th Line in the header part."
                    class="input">{{ $settings->address_and_contact ?? '' }}</textarea>
            </div>
            <div class="col-span-2 mx-2 text-left">
                <label for="description" class="label">Site Description:</label>
                <textarea required type="text" id="description" name="description" autocomplete="description"
                    placeholder="Description For the Site" class="input">{{ $settings->description ?? '' }}</textarea>
            </div>

            <div class="col-span-2 mx-2 text-left">
                <label for="site_logo" class="label">Site Logo:</label>
                <img src="{{ asset('img/logo.png') }}" class="h-24 my-1" alt="Logo">
                <input type="file" id="site_logo" name="site_logo" autocomplete="site_logo" class="input">
            </div>

            <div class="col-span-2 mx-2 text-left">
                <label for="background_image" class="label">Background Image:</label>
                <img src="{{ asset('img/campus.jpg') }}" class="h-24 my-1" alt="Campus">
                <input type="file" id="background_image" name="background_image" autocomplete="background_image"
                    class="input">
            </div>

            <div class="hidden col-span-4 sm:block" aria-hidden="true">
                <div class="py-5">
                    <div class="border-t border-gray-200"></div>
                </div>
            </div>

            <div class="col-span-4">
                <h3 class="mb-3 ml-2 text-lg font-semibold text-left text-gray-700">Admission Settings</h3>
            </div>

            <div class="mx-2 text-left">
                <label for="starting_at" class="label">Admission
                    Starting At:</label>
                <input required type="date" id="starting_at" name="starting_at"
                    value="{{ $settings->starting_at ?? '' }}" autocomplete="starting_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="ending_at" class="label">Admission
                    Ending At:</label>
                <input required type="date" id="ending_at" name="ending_at" value="{{ $settings->ending_at ?? '' }}"
                    autocomplete="ending_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="results_starting_at" class="label">Results
                    Publishing At:</label>
                <input required type="date" id="results_starting_at" name="results_starting_at"
                    value="{{ $settings->results_starting_at ?? '' }}" autocomplete="results_starting_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="results_ending_at" class="label">Results Ending
                    At:</label>
                <input required type="date" id="results_ending_at" name="results_ending_at"
                    value="{{ $settings->results_ending_at ?? '' }}" autocomplete="results_ending_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="dob_starting_at" class="label">Applicable Date of Birth Starting:</label>
                <input required type="date" id="dob_starting_at" name="dob_starting_at"
                    value="{{ $settings->dob_starting_at ?? '' }}" autocomplete="dob_starting_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="dob_ending_at" class="label">Applicable Date of Birth Ending:</label>
                <input required type="date" id="dob_ending_at" name="dob_ending_at"
                    value="{{ $settings->dob_ending_at ?? '' }}" autocomplete="dob_ending_at" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="selectable_max" class="label">Admission Options - Max:</label>
                <input required type="number" id="selectable_max" name="selectable_max"
                    value="{{ $settings->selectable_max ?? '' }}" autocomplete="selectable_max" class="input">
            </div>
            <div class="mx-2 text-left">
                <label for="selectable_min" class="label">Admission Options - Min:</label>
                <input required type="number" id="selectable_min" name="selectable_min"
                    value="{{ $settings->selectable_min ?? '' }}" autocomplete="selectable_min" class="input">
            </div>

            <div class="col-span-2 mx-2 text-left">
                <label for="admission_result_selected_template" class="label">Admission Result Selected
                    Template:</label>
                <textarea required type="text" id="admission_result_selected_template"
                    name="admission_result_selected_template" autocomplete="admission_result_selected_template"
                    placeholder="Admission Result Selected Template"
                    class="input">{{ $settings->admission_result_selected_template ?? '' }}</textarea>
            </div>
            <div class="col-span-2 mx-2 text-left">
                <label for="admission_result_not_selected_template" class="label">Admission Result Non-Selected
                    Template:</label>
                <textarea required type="text" id="admission_result_not_selected_template"
                    name="admission_result_not_selected_template" autocomplete="admission_result_not_selected_template"
                    placeholder="Admission Result Non-Selected Template"
                    class="input">{{ $settings->admission_result_not_selected_template ?? '' }}</textarea>
            </div>
            <div class="flex col-span-4 mx-2 text-left text-gray-600">
                <p class="mr-2">You can use the following variables in the templates:</p>
                <ul>
                    <li><span class="text-pink-600">[ALLOTTED_INSTITUTION]</span> - to show the allotted institution
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <button type="submit" class="px-8 py-2 text-white bg-green-500 rounded-lg hover:bg-green-400">
        Save Changes
    </button>
</form>