@extends('layouts.guest')
@section('main')

@include('layouts.navigation')
<x-alert />

@include('front')

<div x-data="app2()" x-cloak class="block py-6 mx-auto text-black max-w-7xl sm:px-6 lg:px-8">

    <div class="px-4 py-4 my-4 text-center bg-white shadow sm:px-6 rounded-xl">

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

    </div>

    <div class="my-6 bg-white shadow rounded-xl">
        @if (!((\Carbon\Carbon::parse($settings->ending_at ?? '') ?? '') >= today()))
        <form class="block" action="{{ route('status') }}" method="POST">
            @csrf
            @endif
            <x-auth-validation-errors class="mb-4" :errors="$errors" />
            <table class="w-full table-auto rounded-xl">
                <thead class="">
                    <tr class="bg-gray-50 rounded-t-xl">
                        <th class="px-4 py-2 rounded-t-xl" colspan="5">
                            Applications List
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $data)
                    <tr class="text-center border-t">
                        <td class="px-1 py-2 sm:px-4">{{ $data->examcentre->code }}/{{ $data->id }}/2023</td>
                        <td class="px-1 py-2 sm:px-4">{{ $data->name }}</td>
                        @if ((\Carbon\Carbon::parse($settings->ending_at ?? '') ?? '') >= today()) <td
                            class="px-1 py-2 text-center sm:px-4">
                            <div class="flex flex-col items-center">
                                <a target="_blank"
                                    class="block px-4 py-2 my-1 font-semibold text-white bg-blue-600 hover:bg-blue-500 w-max rounded-xl"
                                    href="{{ route('hallticket', ['slug' => $data->slug]) }}">Hall Ticket <i
                                        class="ml-2 fa fa-download"></i></a>
                                <a target="_blank"
                                    class="block px-4 py-2 my-1 font-semibold text-white bg-blue-600 hover:bg-blue-500 w-max rounded-xl"
                                    href="{{ route('applicationPrint', ['slug' => $data->slug]) }}">Application<i
                                        class="ml-2 fa fa-download"></i></a>
                                <a target="_blank"
                                    class="block px-4 py-2 my-1 font-semibold text-white bg-blue-600 hover:bg-blue-500 w-max rounded-xl"
                                    href="{{ route('documents', ['slug' => $data->slug]) }}">Docs <i
                                        class="ml-2 fa fa-download"></i></a>
                                <form action="{{ route('delete', ['id' => $data->id]) }}" method="POST"
                                    @submit.prevent="confirm('Are you sure to delete this application?') && $event.target.submit()">
                                    @csrf
                                    <button type="submit"
                                        class="block px-4 py-2 my-1 font-semibold text-white bg-red-600 hover:bg-red-500 w-max rounded-xl">Delete
                                        <i class="ml-1 fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                        @else
                        <td class="px-1 py-2 sm:px-4">
                            <input type="radio" name="id{{ $data->id }}" class="mr-2 text-red-400 focus:ring-red-400"
                                value="0" @if(!$data->status) checked
                            @endif>
                            <input type="radio" name="id{{ $data->id }}"
                                class="ml-2 text-green-500 focus:ring-green-400" value="1" @if($data->status)
                            checked
                            @endif>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if (!((\Carbon\Carbon::parse($settings->ending_at ?? '') ?? '') >= today()))
            <div class="flex items-center justify-center w-full px-4 py-4">
                <button type="submit" class="px-8 py-2 text-white bg-green-500 rounded-lg hover:bg-green-400">
                    Save Changes
                </button>
            </div>
        </form>
        @endif
    </div>

    {{ $applications->links() }}
</div>

@endsection