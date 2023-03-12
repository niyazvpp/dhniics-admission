<h3 class="mb-3 text-2xl font-semibold text-gray-700">Export Data</h3>

<div class="flex items-center justify-center my-4 form-wrapper">
    <div class="block w-full max-w-xl my-3 text-left">
        <label for="examcentre" class="label">Exam Centre</label>
        <select id="examcentre" x-model="currentFilter" name="examcentre" autocomplete="examcentre" class="input">
            <option value="all">All</option>
            <template x-for="(e, i) in examcentres">
                <option :value="e.name + ' - ' + e.date_time" x-text="e.name + ' - ' + e.date_time"></option>
            </template>
        </select>
    </div>
</div>

<button type="button" class="px-8 py-2 text-white bg-green-500 rounded-lg hover:bg-green-400" @click="exportXLSX()">
    Export Excel File
</button>