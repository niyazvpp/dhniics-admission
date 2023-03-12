@extends('layouts.guest')
@section('main')

@include('layouts.navigation')
<x-alert />

@include('front')

<div x-data="app2()" x-cloak class="block py-6 mx-auto text-black max-w-7xl sm:px-6 lg:px-8">

    @php
    $buttons = array_merge([
    [
    'name' => 'Settings',
    'view' => 'settings'
    ],
    [
    'name' => 'Applications',
    'view' => 'applications'
    ],
    [
    'name' => 'Exam Centres',
    'view' => 'exam_centres'
    ],
    [
    'name' => 'Institution Options',
    'view' => 'institution_options'
    ]], $applicationsAll->count() ? [
    [
    'name' => 'Export',
    'view' => 'export'
    ],
    [
    'name' => 'Results',
    'view' => 'results'
    ]
    ] : []);
    @endphp

    <div class="px-4 py-3 my-4 text-center bg-white shadow sm:px-6 rounded-xl">
        <div class="flex items-center justify-start border-b">
            <template x-for="button in buttons" :key="button.view">
                <a :href="'#' + button.view" :class="{
                    'cursor-default border-b-transparent mb--1': active == button.view,
                    'hover:bg-slate-100 border-transparent': active != button.view
                }" class="text-gray-600 bg-white border border-b-0 rounded-md rounded-b-none shadow-none btn focus:ring-0"
                    @click.prevent="active = button.view" x-text="button.name" type="button"></a>
            </template>
        </div>
        @foreach ($buttons as $button)
        <div id="{{ $button['view'] }}" x-show="active == '{{ $button['view'] }}'" class="p-4 mt-4 mb-6">
            @include('admin.dashboard_' . $button['view'])
        </div>
        @endforeach
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script type="text/javascript">
    function app2() {
        var data = <?= $applicationsAll->toJson() ?>;
        var examcentres = @json($examcentres);
        var buttons = {!! json_encode($buttons) !!};
        data = data.map(n => {
            var centre = examcentres.find(centre => n.exam_centre_id == centre.id);
            var e = {
                "Ref. No.": n.id,
                "Name": n.name,
                "Address": [n.address, n.city, n.state, n.postalcode + ' PIN'].join(', '),
                "DOB": n.dob,
                "Guardian": n.guardian,
                "Mobile No.": n.mobile,
                "Alt. Mobile": n.mobile2,
                "Email": n.email,
                "Exam Centre & Date": centre.name + ' - ' + centre.date_time,
                "Makthab": n.makthab_years ? n.makthab_years + ' Years' : 'No'
            };
            n.institutions.forEach((i, x) => {
                e['Admission Option ' + (x + 1)] = i.name;
            });
            return e;
        });
        var hash = window.location.hash || sessionStorage.getItem('active') || '#settings';
        var id = hash.replace('#', '');
        var button = buttons.find(b => b.view == id) || buttons[0];
        active = button.view;
        return {
            init() {
                this.$watch('active', () => {
                    sessionStorage.setItem('active', this.active);
                    this.$nextTick(() => {
                        window.location.hash = this.active;
                    });
                });
            },
            buttons: buttons,
            active: active,
            data: data,
            examcentres: examcentres,
            institutions: @json($institutions),
            currentFilter: 'all',
            filteredData() {
                var filter = this.currentFilter;
                if (filter == 'all') return this.data;
                var data = this.data.filter(e => e["Exam Centre & Date"] == filter);
                return data;
            },
            exportXLSX() {
                var data = this.filteredData();
                if (! data.length) {
                alert('No Applications on given centre!');
                return false;
                }
                const worksheet = XLSX.utils.json_to_sheet(data);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Applications");

                worksheet["!cols"] = [];
                var max_width;
                for (d in data[0]) {
                max_width = data.reduce((w, r) => Math.max(w, typeof r != 'string' ? `${r[d]}`.length : r[d].length ), 10);
                worksheet["!cols"].push({ wch: max_width });
                }

                /* create an XLSX file and try to save to Presidents.xlsx */
                XLSX.writeFile(workbook, `DHBC-Applications-${this.currentFilter}-2023-24.xlsx`);
            },
            exam_centre_modal_show: false,
            exam_centre_modal_id: null,
            exam_centre_modal_action: '',
            exam_centre_modal_name: '',
            exam_centre_loading: false,
            exam_centre_selected: {
                id: null,
                code: '',
                name: '',
                date_time: '',
                address: '',
                start_date: '',
                end_date: '',
            },
            add_new() {
                this.exam_centre_modal_show = true;
                this.exam_centre_modal_id = null;
                this.exam_centre_modal_action = '{{ route('exam_centres.create') }}';
                this.exam_centre_modal_name = 'Add New Exam Centre';
                this.exam_centre_selected = {
                    id: null,
                    code: '',
                    name: '',
                    date_time: '',
                    address: '',
                    start_date: '',
                    end_date: '',
                };
            },
            close() {
                this.exam_centre_modal_show = false;
            },
            edit(id) {
                this.exam_centre_modal_show = true;
                this.exam_centre_modal_id = id;
                this.exam_centre_modal_action = '{{ route('exam_centres.update', '') }}/' + id;
                this.exam_centre_modal_name = 'Edit Exam Centre';
                this.exam_centre_selected = this.examcentres.find(e => e.id == id);
            },
            submit(form) {
                this.exam_centre_loading = true;
                var data = new FormData(form);
                // convert any datetime values to Y-m-d H:i:s format
                var items = document.querySelectorAll('[type="datetime-local"]');
                items.forEach(item => {
                    var name = item.name;
                    var value = item.value;
                    if (value) {
                        var date = new Date(value);
                        var year = date.getFullYear();
                        var month = date.getMonth() + 1;
                        var day = date.getDate();
                        var hours = date.getHours();
                        var minutes = date.getMinutes();
                        var seconds = date.getSeconds();
                        // add 0 if single digit
                        month = month < 10 ? '0' + month : month;
                        day = day < 10 ? '0' + day : day;
                        hours = hours < 10 ? '0' + hours : hours;
                        minutes = minutes < 10 ? '0' + minutes : minutes;
                        seconds = seconds < 10 ? '0' + seconds : seconds;
                        var formatted = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                        data.set(name, formatted);
                    }
                });
                var url = this.exam_centre_modal_action;
                form.querySelectorAll(".form-group").forEach(e => e.classList.remove("validated"));
                var method = 'POST';
                axios({
                    method: method,
                    url: url,
                    data: data,
                })
                .then(response => {
                    this.exam_centre_loading = false;
                    console.log(response)
                    if (response.data.success) {
                        this.exam_centre_modal_show = false;
                        this.exam_centre_modal_action = '';
                        this.exam_centre_modal_name = '';
                        this.exam_centre_selected = {
                        id: null,
                        code: '',
                        name: '',
                        date_time: '',
                        address: '',
                        start_date: '',
                        end_date: '',
                        };
                        var message = 'Exam Centre Added Successfully!';
                        if (response.status == 200) {
                            message = 'Exam Centre Updated Successfully!';
                            // update the exam centre in the list
                            var index = this.examcentres.findIndex(e => e.id == this.exam_centre_modal_id);
                            this.examcentres[index] = response.data.data;
                        } else {
                            // add the exam centre to the list
                            this.examcentres.push(response.data.data);
                        }
                        alert(message);
                        this.currentFilter = 'all';
                        this.exam_centre_modal_id = null;
                    }
                })
                .catch((error) => {
                    this.exam_centre_loading = false;
                    console.log(error.response.data);
                    if (error.response && error.response.data && (errors = error.response.data.errors)) {
                        Object.keys(errors).forEach(name => {
                            var obj = form.querySelector("[name=" + name +"]");
                            var error = errors[name][0];
                            if (!obj) return;
                            obj.closest(".form-group").classList.add("validated");
                            obj.closest(".form-group").querySelector(".error").innerHTML = error;
                        });
                    }
                });
            },
            remove(id) {
                if (!confirm('Are you sure you want to delete this exam centre?')) {
                    return false;
                }
                var url = '{{ route('exam_centres.delete', '') }}/' + id;
                axios({
                    method: 'POST',
                    url: url,
                })
                .then(response => {
                    console.log(response)
                    if (response.data.success) {
                        var index = this.examcentres.findIndex(e => e.id == id);
                        this.examcentres.splice(index, 1);
                        alert('Exam Centre Deleted Successfully!');
                    }
                })
                .catch((error) => {
                    if (error.response.data && error.response.data.message) {
                        alert(error.response.data.message);
                    }
                    if (error.response && error.response.data && (errors = error.response.data.errors)) {
                        Object.keys(errors).forEach(name => {
                            var obj = form.querySelector("[name=" + name +"]");
                            var error = errors[name][0];
                            if (!obj) return;
                            obj.closest(".form-group").classList.add("validated");
                            obj.closest(".form-group").querySelector(".error").innerHTML = error;
                        });
                    }
                });
            },

            institution_modal_show: false,
            institution_modal_id: null,
            institution_modal_action: '',
            institution_modal_name: '',
            institution_loading: false,
            institution_selected: {
                id: null,
                code: '',
                name: '',
                date_time: '',
                address: '',
                quota: '',
            },
            add_new_institution() {
                this.institution_modal_show = true;
                this.institution_modal_id = null;
                this.institution_modal_action = '{{ route('institutions.create') }}';
                this.institution_modal_name = 'Add New Institution';
                this.institution_selected = {
                    id: null,
                    code: '',
                    name: '',
                    date_time: '',
                    address: '',
                    quota: '',
                };
            },
            institution_close() {
                this.institution_modal_show = false;
            },
            institution_edit(id) {
                this.institution_modal_show = true;
                this.institution_modal_id = id;
                this.institution_modal_action = '{{ route('institutions.update', '') }}/' + id;
                this.institution_modal_name = 'Edit Institution';
                this.institution_selected = this.institutions.find(e => e.id == id);
            },
            institution_submit(form) {
                this.institution_loading = true;
                var data = new FormData(form);
                // convert any datetime values to Y-m-d H:i:s format
                var items = document.querySelectorAll('[type="datetime-local"]');
                items.forEach(item => {
                    var name = item.name;
                    var value = item.value;
                    if (value) {
                        var date = new Date(value);
                        var year = date.getFullYear();
                        var month = date.getMonth() + 1;
                        var day = date.getDate();
                        var hours = date.getHours();
                        var minutes = date.getMinutes();
                        var seconds = date.getSeconds();
                        // add 0 if single digit
                        month = month < 10 ? '0' + month : month;
                        day = day < 10 ? '0' + day : day;
                        hours = hours < 10 ? '0' + hours : hours;
                        minutes = minutes < 10 ? '0' + minutes : minutes;
                        seconds = seconds < 10 ? '0' + seconds : seconds;
                        var formatted = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                        data.set(name, formatted);
                    }
                });
                var url = this.institution_modal_action;
                form.querySelectorAll(".form-group").forEach(e => e.classList.remove("validated"));
                var method = 'POST';
                axios({
                    method: method,
                    url: url,
                    data: data,
                })
                .then(response => {
                    this.institution_loading = false;
                    console.log(response)
                    if (response.data.success) {
                        this.institution_modal_show = false;
                        this.institution_modal_action = '';
                        this.institution_modal_name = '';
                        this.institution_selected = {
                        id: null,
                        code: '',
                        name: '',
                        date_time: '',
                        address: '',
                        quota: '',
                        };
                        var message = 'Institution Added Successfully!';
                        if (response.status == 200) {
                            message = 'Institution Updated Successfully!';
                            // update the institution in the list
                            var index = this.institutions.findIndex(e => e.id == this.institution_modal_id);
                            this.institutions[index] = response.data.data;
                        } else {
                            // add the institution to the list
                            this.institutions.push(response.data.data);
                        }
                        alert(message);
                        this.currentFilter = 'all';
                        this.institution_modal_id = null
                    }
                })
                .catch((error) => {
                    this.institution_loading = false;
                    console.log(error.response.data);
                    if (error.response && error.response.data && (errors = error.response.data.errors)) {
                        Object.keys(errors).forEach(name => {
                            var obj = form.querySelector("[name=" + name +"]");
                            var error = errors[name][0];
                            if (!obj) return;
                            obj.closest(".form-group").classList.add("validated");
                            obj.closest(".form-group").querySelector(".error").innerHTML = error;
                        });
                    }
                });
            },
            institution_delete(id) {
                if (!confirm('Are you sure you want to delete this institution?')) {
                    return false;
                }
                var url = '{{ route('institutions.delete', '') }}/' + id;
                axios({
                    method: 'POST',
                    url: url,
                })
                .then(response => {
                    console.log(response)
                    if (response.data.success) {
                        var index = this.institutions.findIndex(e => e.id == id);
                        this.institutions.splice(index, 1);
                        alert('Institution Deleted Successfully!');
                    }
                })
                .catch((error) => {
                    if (error.response.data && error.response.data.message) {
                        alert(error.response.data.message);
                    }
                    if (error.response && error.response.data && (errors = error.response.data.errors)) {
                        Object.keys(errors).forEach(name => {
                            var obj = form.querySelector("[name=" + name +"]");
                            var error = errors[name][0];
                            if (!obj) return;
                            obj.closest(".form-group").classList.add("validated");
                            obj.closest(".form-group").querySelector(".error").innerHTML = error;
                        });
                    }
                });
            },
        }
    }
</script>
@endsection