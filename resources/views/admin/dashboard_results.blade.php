<h3 class="mb-2 text-2xl font-semibold text-gray-700">Allotments</h3>
<div class="my-6 bg-white shadow rounded-xl">
    <form class="block" action="{{ route('status') }}" method="POST">
        @csrf
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <table class="w-full table-auto rounded-xl">
            <tbody>
                @foreach($applications as $data)
                <tr class="text-center border-t">
                    <td class="px-1 py-2 sm:px-4">{{ $data->examcentre->code }}/{{ $data->id }}/2023</td>
                    <td class="px-1 py-2 sm:px-4">{{ $data->name }}</td>
                    <td class="px-1 py-2 sm:px-4">
                        @if ($settings->selectable_max > 0)
                        <select name="ids[{{ $data->id }}]" id="allotment_id" class="mb-0 input">
                            <option value="">-- Not Selected --</option>
                            @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" @if($data->allotment_id == $institution->id)
                                selected
                                @endif>{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <select name="ids[{{ $data->id }}]" id="allotment" class="input">
                            <option value="">Not Allotted</option>
                            <option value="1" @if($data->status)
                                selected
                                @endif>Allotted</option>
                        </select>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex items-center justify-center w-full px-4 py-4">
            <button type="submit" class="px-8 py-2 text-white bg-green-500 rounded-lg hover:bg-green-400">
                Save Changes
            </button>
        </div>
    </form>
</div>

{{ $applications->links() }}