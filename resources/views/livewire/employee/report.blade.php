<div>
    <div class="flex flex-row mb-4">
        <div class="flex justify-end flex-1 ">
            <div class="mr-2">
                <x-date-picker wire:model.defer="dateFilter.fromDate" />
                <x-jet-input-error for="fromDate" class="mt-2" />
            </div>
            <div class="mr-2">
                <x-date-picker wire:model.defer="dateFilter.toDate" />
                <x-jet-input-error for="toDate" class="mt-2" />
            </div>
            <div>
                <x-jet-button class="py-3 px-4" wire:click="filterReport">
                    {{ __('Filter') }}
                </x-jet-button>
            </div>
             <div>
                <x-jet-button class="py-3 px-4 ml-1" wire:click="filterUntilToday">
                    {{ __('Until today') }}
                </x-jet-button>
            </div>
        </div>
    </div>

    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Day') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Planned') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Is') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Away') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Diff') }}
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Running Balance') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @can('switchEmployee',  [App\Model\Report::class, $location])
                            <tr>
                                <td class="bg-white">
                                    <x-simple-select
                                        wire:model="employeeIdToBeSwitched" wire:change="switchEmployee" id="employeeSwitcher" :options="$employeeSwitcher" />
                                </td>
                                <td colspan="6" class="bg-white">
                                </td>
                            </tr>
                        @endcan
                        @if($this->report())
                            <tr>
                                <td class="px-6 py-2 whitespace-no-wrap">
                                    {{ $this->report()->startRow->label() }}
                                </td>
                                <td colspan="5">
                                    &nbsp;
                                </td>
                                <td class="px-6 py-2 whitespace-no-wrap">
                                    {{ $this->report()->startRow->balance() }}
                                </td>
                            </tr>
                            @foreach ($this->report()->reportRows as $row)
                                <tr class="{{ $row->labelColor() }}">
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        <div class="inline-flex">
                                            {{ $row->name() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        <div class="inline-flex">
                                        {{ $row->label() }}
                                        @if($row->publicHolidayLabel())
                                            <div class="ml-1 relative info-trigger">
                                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="25"
                                                    viewBox="0 0 580 580" style="enable-background:new 0 0 580 580;" xml:space="preserve">
                                                <path id="XMLID_1607_" d="M214.991,300c0-5.522,4.477-10,10-10h2c5.523,0,10,4.478,10,10s-4.477,10-10,10h-2
                                                    C219.468,310,214.991,305.522,214.991,300z M478.672,177.107c-2.448,6.438-7.841,11.003-14.338,12.425
                                                    c3.582,5.991,5.643,12.994,5.643,20.468c0,22.056-17.943,40-39.999,40H416.23c-4.827,15.655-14.061,29.394-26.251,39.761
                                                    c0.002,0.079,0.003,0.159,0.003,0.239c0,57.443-32.46,107.451-79.996,132.643V470c0,5.522-4.477,10-10,10s-10-4.478-10-10v-38.567
                                                    C274.342,436.979,257.511,440,239.99,440c-17.522,0-34.353-3.021-49.997-8.567V470c0,5.522-4.477,10-10,10s-10-4.478-10-10v-47.357
                                                    C122.457,397.451,89.998,347.443,89.998,290c0-0.08,0.001-0.16,0.003-0.239C77.812,279.394,68.577,265.655,63.75,250h-13.75
                                                    c-22.055,0-39.998-17.944-39.998-40c0-7.476,2.062-14.479,5.646-20.472c-6.488-1.426-11.875-5.989-14.32-12.421
                                                    c-2.977-7.832-0.854-16.489,5.408-22.056c0.105-0.093,0.212-0.185,0.32-0.273l72.942-59.525V50c0-27.57,22.429-50,49.997-50h219.988
                                                    c27.569,0,49.997,22.43,49.997,50v45.253l72.961,59.525c0.109,0.089,0.216,0.18,0.321,0.273
                                                    C479.526,160.618,481.649,169.275,478.672,177.107z M99.998,90h279.983V50c0-16.542-13.457-30-29.998-30H129.996
                                                    c-16.541,0-29.998,13.458-29.998,30V90z M60.145,230c-0.097-1.655-0.146-3.32-0.146-5v-35h-9.999c-11.027,0-19.998,8.972-19.998,20
                                                    s8.971,20,19.998,20H60.145z M144.995,290c35.839,0,64.996-29.159,64.996-65v-35h-53.816l-27.234,54.472
                                                    c-2.47,4.939-8.475,6.942-13.416,4.473c-4.939-2.47-6.942-8.477-4.472-13.416L133.815,190H80v35C80,260.841,109.157,290,144.995,290
                                                    z M369.364,302.734c-10.517,4.67-22.15,7.266-34.379,7.266c-41.749,0-76.565-30.261-83.67-70h-22.649
                                                    c-7.105,39.739-41.921,70-83.67,70c-12.229,0-23.862-2.596-34.379-7.266C117.036,368.462,172.608,420,239.99,420
                                                    S362.944,368.462,369.364,302.734z M249.989,220v-30h-19.998v30H249.989z M399.98,190h-53.816l-27.234,54.472
                                                    c-2.47,4.939-8.475,6.942-13.416,4.473c-4.939-2.47-6.942-8.477-4.472-13.416L323.804,190h-53.815v35c0,35.841,29.157,65,64.996,65
                                                    s64.996-29.159,64.996-65V190z M449.978,210c0-11.028-8.972-20-19.999-20h-9.999v35c0,1.68-0.049,3.345-0.146,5h10.144
                                                    C441.006,230,449.978,221.028,449.978,210z M459.962,170l-73.542-60H93.561l-73.523,60H459.962z M155.523,248.944
                                                    c1.436,0.718,2.961,1.058,4.464,1.058c3.668,0,7.2-2.026,8.952-5.53l9.999-20c2.47-4.939,0.467-10.946-4.472-13.416
                                                    c-4.941-2.47-10.947-0.468-13.416,4.473l-9.999,20C148.581,240.468,150.583,246.475,155.523,248.944z M297.058,332.929
                                                    c1.875,1.876,2.929,4.419,2.929,7.071c0,33.084-26.914,60-59.997,60s-59.997-26.915-59.997-59.998c0-5.522,4.477-10,10-10
                                                    L289.987,330C292.639,330,295.183,331.054,297.058,332.929z M278.722,350h-77.464c4.452,17.233,20.129,30,38.732,30
                                                    S274.271,367.232,278.722,350z M345.512,248.944c1.436,0.718,2.961,1.058,4.464,1.058c3.668,0,7.2-2.026,8.952-5.53l9.999-20
                                                    c2.47-4.939,0.467-10.946-4.472-13.416c-4.942-2.47-10.947-0.468-13.416,4.473l-9.999,20
                                                    C338.57,240.468,340.572,246.475,345.512,248.944z M252.989,310h2c5.523,0,10-4.478,10-10s-4.477-10-10-10h-2
                                                    c-5.523,0-10,4.478-10,10S247.466,310,252.989,310z"/></svg>
                                                <ul class="absolute left-0 top-0 -ml-3 mt-7 p-2 rounded-lg shadow-lg bg-white z-10 info-target hidden">
                                                    <svg class="block fill-current text-white w-4 h-4 absolute left-0 top-0 ml-3 -mt-3 z-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>
                                                        <li class="p-1 whitespace-no-wrap rounded-full text-sm md:text-base text-gray-600">
                                                            {{ __('Public holiday:') }} {{ $row->publicHolidayLabel() }}
                                                        </li>
                                                </ul>
                                            </div>
                                        @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        {{ $row->plannedHours() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        {{ $row->workingHours() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        <div class="inline-flex">
                                            <div>
                                                {{ $row->absentHours() }}
                                            </div>
                                            @if(!$row->absentHoursCollection()->isEmpty())
                                                <div class="ml-1 relative info-trigger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <ul class="absolute left-0 top-0 -ml-3 mt-7 p-2 rounded-lg shadow-lg bg-white z-10 info-target hidden">
                                                        <svg class="block fill-current text-white w-4 h-4 absolute left-0 top-0 ml-3 -mt-3 z-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>
                                                        @foreach($row->absentHoursCollection() as $absentHour)
                                                            <li class="p-1 whitespace-no-wrap rounded-full text-sm md:text-base text-gray-600">
                                                                <span class="p-1">{{ $absentHour->absenceType->title }}: {{$absentHour->hours}}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        {{ $row->diff() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-no-wrap">
                                        {{ $row->balance() }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
