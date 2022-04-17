<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('EXCEL Import') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <x-alert.success></x-alert.success>

                    <x-validation-errors class="mb-4" :errors="$errors"/>

                    <form action="{{ route('import_parse') }}" method="POST" class="mb-4" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-label for="csv_file" :value="__('CSV file to import')"/>

                            <x-input id="csv_file" class="block mt-1 w-full" type="file" name="csv_file" required/>
                        </div>

                        <div class="mt-4 flex items-center">
                            <x-label for="header" :value="__('File contains header row?')"/>

                            <x-input id="header" class="ml-1" type="checkbox" name="header" checked/>
                        </div>

                        <x-button class="mt-4">
                            {{ __('Submit') }}
                        </x-button>
                    </form>

                    <div class="overflow-x-auto min-w-full align-middle sm:rounded-md">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead>
                            <tr>
                                @foreach(config('app.db_fields') as $index => $field)
                                    <th class="px-6 py-3 bg-gray-50">
                                        <span class="text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{ $field }}</span>
                                    </th>
                                @endforeach
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                            @foreach($contacts as $contact)
                                <tr class="bg-white">
                                    @foreach(config('app.db_fields') as $index => $field)
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                            {{ strlen($contact[$field]) > 30 ? substr($contact[$field], 0, 30)."...." : $contact[$field] }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $contacts->links() }}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>