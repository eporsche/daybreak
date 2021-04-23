<div >
    <div x-data="dropdown()">
        <div class="relative" wire:ignore>
            <div class="flex flex-col items-center relative m-1 p-1">
                <div class="w-full">
                    <div :class="{
                            'm-1 p-1 flex border border-gray-800 bg-white rounded ring-1 ring-blue-600': show,
                            'm-1 p-1 flex border border-gray-800 bg-white rounded': !show
                        }">
                        <div class="flex flex-auto flex-wrap">
                            <template x-for="(option,index) in selected" :key="option[trackBy]">
                                <div
                                    class="flex justify-center items-center m-1 font-medium py-1 px-1  rounded  border">
                                    <div class="font-normal leading-none max-w-full flex-initial px-1"
                                         x-text="option.title"></div>
                                    <div class="flex flex-auto flex-row-reverse">
                                        <div x-on:click.stop="remove(option)">
                                            <svg class="w-4 h-4 cursor-pointer" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div class="flex-1">
                                <div x-show="!selected.length" class="bg-transparent p-1 px-2 appearance-none outline-none h-full w-full text-gray-800">
                                    {{$attributes->get('placeholder') ?: __('Select Filter')}}
                                </div>
                            </div>
                        </div>
                        <div class="text-gray-300 w-8 py-1 pl-2 pr-1  flex items-center border-gray-200">
                            <button type="button"
                                    class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                                <svg x-show="show" x-on:click="close" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <svg x-show="!show" x-on:click="open" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"></path>
                                    <!-- <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 15l7-7 7 7"></path> -->
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full mt-0" x-show.transition.origin.top="show===true" x-on:click.away="close">
                    <div class="absolute shadow top-100 bg-white z-40 w-full left-0 rounded max-h-select">
                        <div class="flex flex-col w-full overflow-y-auto max-h-64">
                            <template x-for="(option,index) in options" :key="option[trackBy]" class="overflow-auto">
                                <div
                                    class="cursor-pointer w-full border-gray-100 rounded"
                                    @click="select(option)">
                                    <div class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                        <div class="w-full items-center flex justify-between">
                                            <div class="mx-2 leading-6" x-text="option.title"></div>
                                            <svg x-show="inSelected(option)" class="w-4 h-4 cursor-pointer" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="!options.length"
                                 class="cursor-pointer w-full border-gray-100 rounded border-b border-solid ">
                                <div class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                    <div class="w-full items-center flex justify-between">
                                        <div class="mx-2 leading-6">{{ __('List is empty') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function dropdown() {
        return {
            options: @json($options) ? @json($options) : [],
            selected: @entangle($attributes->wire('model')),
            trackBy:@json($trackBy),
            title:@json($label),
            show: false,
            open() {
                this.show = true;
            },
            close() {
                this.show = false;
            },
            select(option, $dispatch) {
                this.inSelected(option) ? this.remove(option, $dispatch) : this.selected.push(option);
            },
            inSelected(option) {
                return this.selected.find(item => item[this.trackBy] == option[this.trackBy])
            }
            ,
            remove(option) {
                this.options = this.options.map((item) => {
                    item.selected = item[this.trackBy] == option[this.trackBy] ? false : true;
                    return item;
                })
                this.selected = this.selected.filter(item => item[this.trackBy] != option[this.trackBy])
            }
        }
    }
</script>
