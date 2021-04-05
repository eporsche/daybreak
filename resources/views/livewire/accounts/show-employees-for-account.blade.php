<div>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Name') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->account->users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            <a class="text-indigo-600 hover:text-indigo-900" href="{{ route('employees.edit', ['account' => $account, 'employee' => $user])}}">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>

                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-2">
                                {{ __('no data yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
</div>
