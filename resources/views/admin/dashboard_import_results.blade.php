<h3 class="mb-3 text-2xl font-semibold text-gray-700">Import Results</h3>

<x-auth-validation-errors class="mb-4" :errors="$errors" />

<form action="{{ route('results.import') }}" method="POST" class="block max-w-lg mx-auto" enctype="multipart/form-data">
    @csrf
    <label for="excel_file" class="text-left label">Select Excel File</label>
    <input type="file" name="excel_file" id="excel_file" accept=".xlsx" class="input" required>
    <button type="submit" class="px-8 py-2 text-white bg-green-500 rounded-lg hover:bg-green-400">
        Import From Excel
    </button>
</form>