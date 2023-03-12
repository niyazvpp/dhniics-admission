<h3 class="mb-2 text-2xl font-semibold text-gray-700">Institutions</h3>
<div class="my-6 bg-white shadow rounded-xl">
    <table class="w-full table-auto rounded-xl">
        <thead class="">
            <tr class="bg-gray-50 rounded-t-xl">
                <th class="px-4 py-3 rounded-tl-xl">Code</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Quota</th>
                <th class="px-4 py-3 rounded-tr-xl">Action</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="centre in institutions" :key="centre.id">
                <tr class="text-center border-t">
                    <td class="px-1 py-3 sm:px-4" x-text="centre.code"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.name"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.address"></td>
                    <td class="px-1 py-3 sm:px-4" x-text="centre.quota"></td>
                    <td class="px-1 py-3 sm:px-4">
                        <button @click.prevent="institution_edit(centre.id)"
                            class="text-blue-400 bg-gray-100 border shadow-none btn">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button @click.prevent="institution_delete(centre.id)"
                            class="text-red-400 bg-gray-100 border shadow-none btn">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </template>
            <template x-if="!institutions.length">
                <tr class="text-center border-t">
                    <td class="px-1 py-3 sm:px-4" colspan="5">No Institutions Available!</td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

<button @click.prevent="add_new_institution()" type="button" class="text-sm btn btn-green">
    <i class="mr-2 fa fa-plus"></i>Add New
</button>

<!--- Add new exam centre modal --->
<div x-show="institution_modal_show" class="fixed inset-0 z-10 overflow-y-auto modal" aria-labelledby="modal-title"
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
            x-show="institution_modal_show" x-transition:enter="transition ease-out duration-300"
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
            x-show="institution_modal_show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="px-4 pt-5 pb-4 bg-white form-container sm:p-6 sm:pb-4">
                <h3 x-text="institution_modal_name" class="mb-3 text-xl font-medium h3"></h3>
                <form class="block" :action="institution_modal_action" method="POST">
                    @csrf
                    <div class="grid grid-cols-3">
                        <div class="col-span-2 form-group">
                            <label for="name" class="label">Name:</label>
                            <input name="name" :value="institution_selected.name" id="name" type="text" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="form-group">
                            <label for="code" class="label">Code:</label>
                            <input name="code" :value="institution_selected.code" id="code" type="text" class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="col-span-3 form-group">
                            <label for="address" class="label">Address:</label>
                            <textarea name="address" :value="institution_selected.address" id="address" type="text"
                                class="input"></textarea>
                            <small class="text-red-500 error"></small>
                        </div>
                        <div class="col-span-2 form-group">
                            <label for="quota" class="label">Quota:</label>
                            <input name="quota" :value="institution_selected.quota" id="quota" type="number"
                                class="input">
                            <small class="text-red-500 error"></small>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button :disabled="institution_loading"
                    :class="{ 'hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-blue-400': !institution_loading, 'opacity-40': institution_loading }"
                    x-text="institution_loading ? 'Loading...' : !institution_modal_id ? 'Add Exam Centre' : 'Save Changes'"
                    @click.prevent="institution_submit($event.target.closest('.modal').querySelector('form'))"
                    type="button"
                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-400 border border-transparent rounded-md shadow-sm focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add
                    Exam Centre</button>
                <button @click="institution_close()" type="button"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>