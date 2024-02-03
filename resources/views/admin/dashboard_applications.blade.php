<h3 class="mb-2 text-2xl font-semibold text-gray-700">Delete All Applications</h3>
<form method="POST" action="{{ route('destroy') }}"
      @submit.prevent="confirm('You are about to delete all Applications! Are you sure?') && $event.target.submit()">
    @csrf
    <div class="flex items-center justify-center my-4 form-wrapper">
        <div class="block w-full max-w-xl">
            <label for="password" class="label">Confirm
                Password:</label>
            <input required type="password" name="password" id="password" autocomplete="password" class="input">
            <p class="mt-1 text-sm text-left text-gray-600">Every Application will be deleted and ref
                and roll numbers will be reset to 101 and 1001 respectively.</p>
        </div>
    </div>
    <button type="submit" class="px-8 py-2 text-white bg-red-600 rounded-lg hover:bg-red-500">
        Confirm
    </button>
</form>

<div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
        <div class="border-t border-gray-200"></div>
    </div>
</div>

<h3 class="mb-2 text-2xl font-semibold text-gray-700">Applications List</h3>
<div class="my-6 bg-white shadow rounded-xl">
    <x-auth-validation-errors class="mb-4" :errors="$errors" />
    <table class="w-full table-auto rounded-xl">
        <thead class="rounded-t-xl">
            <tr class="text-left bg-gray-200 border-b rounded-t-xl">
                <th class="px-1 py-2 text-center sm:px-4 rounded-tl-xl">Ref No.</th>
                <th class="px-1 py-2 text-center sm:px-4">Name</th>
                <th class="px-1 py-2 text-center sm:px-4 rounded-tr-xl">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $data)
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
                            <form action="{{ route('delete', ['id' => $data->id]) }}" method="POST"
                                  @submit.prevent="confirm('Are you sure to delete this application?') && $event.target.submit()">
                                @csrf
                                <button type="submit"
                                        class="mx-2 text-red-500 bg-gray-100 border shadow-none btn hover:bg-gray-200 hover:text-red-600">Delete
                                    <i class="ml-1 fa fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $applications->links() }}
