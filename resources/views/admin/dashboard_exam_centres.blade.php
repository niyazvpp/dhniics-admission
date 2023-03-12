<h3 class="mb-2 text-2xl font-semibold text-gray-700">Exam Centres</h3>
<!--- List out exam centres in a table with code, name, address, starting time, ending time --->
<div class="my-6 bg-white shadow rounded-xl">
    <table class="w-full table-auto rounded-xl">
        <thead class="">
            <tr class="bg-gray-50 rounded-t-xl">
                <th class="px-4 py-3 rounded-tl-xl">Code</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Starting Time</th>
                <th class="px-4 py-3">Ending Time</th>
                <th class="px-4 py-3 rounded-tr-xl">Action</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="centre in examcentres" :key="centre.id">
                <tr class="text-center border-t">
                    <td class="px-1 py-3 sm:px-4" x-text="centre.code"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.name"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.address"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.start_date"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.end_date"></td>
                    <td class="px-1 py-3 sm:px-4">
                        <button @click.prevent="edit(centre.id)"
                            class="text-blue-400 bg-gray-100 border shadow-none btn">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button @click.prevent="remove(centre.id)"
                            class="text-red-400 bg-gray-100 border shadow-none btn">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </template>
            <template x-if="!examcentres.length">
                <tr class="text-center border-t">
                    <td class="px-1 py-3 sm:px-4" colspan="5">No Exam Centres Available!</td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

<button @click.prevent="add_new()" type="button" class="text-sm btn btn-green">
    <i class="mr-2 fa fa-plus"></i>Add New
</button>

<!--- Add new exam centre modal --->
<div x-show="exam_centre_modal_show" class="fixed inset-0 z-10 overflow-y-auto modal" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!--
        Background overlay, show/hide based on modal state.

        Entering: "ease-out duration-300"
          From: "opacity-0"
          To: "opacity-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100"
          To: "opacity-0"
      -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
            x-show="exam_centre_modal_show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!--
        Modal panel, show/hide based on modal state.

        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
        <div class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
            x-show="exam_centre_modal_show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="px-4 pt-5 pb-4 bg-white form-container sm:p-6 sm:pb-4">
                <h3 x-text="exam_centre_modal_name" class="mb-3 text-xl font-medium h3"></h3>
                <form class="block" :action="exam_centre_modal_action" method="POST">
                    @csrf
                    <div class="grid grid-cols-3">
                        <div class="col-span-2 form-group">
                            <label for="name" class="label">Name:</label>
                            <input name="name" :value="exam_centre_selected.name" id="name" type="text" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="form-group">
                            <label for="code" class="label">Code:</label>
                            <input name="code" :value="exam_centre_selected.code" id="code" type="text" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="col-span-3 form-group">
                            <label for="address" class="label">Address:</label>
                            <textarea name="address" :value="exam_centre_selected.address" id="address" type="text"
                                class="input"></textarea>
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="form-group">
                            <label for="date_time" class="label">Date & Time:</label>
                            <input name="date_time" :value="exam_centre_selected.date_time" id="date_time"
                                type="datetime-local" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="form-group">
                            <label for="start_date" class="label">Starting At:</label>
                            <input name="start_date" :value="exam_centre_selected.start_date" id="start_date"
                                type="date" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="label">Ending At:</label>
                            <input name="end_date" :value="exam_centre_selected.end_date" id="end_date" type="date"
                                class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button :disabled="exam_centre_loading"
                    :class="{ 'hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-blue-400': !exam_centre_loading, 'opacity-40': exam_centre_loading }"
                    x-text="exam_centre_loading ? 'Loading...' : !exam_centre_modal_id ? 'Add Exam Centre' : 'Save Changes'"
                    @click.prevent="submit($event.target.closest('.modal').querySelector('form'))" type="button"
                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-400 border border-transparent rounded-md shadow-sm focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add
                    Exam Centre</button>
                <button @click="close()" type="button"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>