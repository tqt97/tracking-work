<x-app-layout>

    <div class="p-6 max-w-5xl mx-auto"
        x-data="{ openModal: false, editMode: false, form: {id: '', name: '', slug: '', description: ''} }">
        <h1 class="text-2xl font-bold mb-6">Quản lý Vai Trò</h1>

        <!-- Add Button -->
        <div class="flex justify-between mb-4">
            <button @click="editMode=false; openModal=true; form={id:'',name:'',slug:'',description:''}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                + Thêm vai trò
            </button>

            <a href="{{ route('admin.roles.permissions') }}" class="text-blue-600 hover:underline text-sm mt-2">
                → Cấu hình quyền cho vai trò
            </a>
        </div>

        <!-- Flash message -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded-md">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded-md">{{ session('error') }}</div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Tên vai trò</th>
                        <th class="px-4 py-2">Slug</th>
                        <th class="px-4 py-2">Mô tả</th>
                        <th class="px-4 py-2 text-center w-32">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $i => $role)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $i + 1 }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $role->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $role->slug }}</td>
                            <td class="px-4 py-2">{{ $role->description ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        @click="openModal=true; editMode=true; form={id:{{ $role->id }}, name:'{{ $role->name }}', slug:'{{ $role->slug }}', description:'{{ $role->description }}'}"
                                        class="text-blue-600 hover:underline text-sm">Sửa</button>

                                    @if($role->slug !== 'admin')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                            onsubmit="return confirm('Xóa vai trò này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm">Xóa</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="openModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40">
            <div @click.away="openModal=false" class="bg-white rounded-lg w-[420px] p-5 shadow-lg">
                <h2 class="text-lg font-semibold mb-3" x-text="editMode ? 'Chỉnh sửa vai trò' : 'Thêm vai trò mới'">
                </h2>
                <form :action="editMode ? `/admin/roles/${form.id}` : '{{ route('admin.roles.store') }}'" method="POST"
                    class="space-y-3">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div>
                        <label class="text-sm text-gray-600">Tên vai trò</label>
                        <input type="text" name="name" x-model="form.name"
                            class="w-full border-gray-300 rounded-md text-sm px-2 py-1.5 focus:ring-blue-300" required>
                    </div>

                    <template x-if="!editMode">
                        <div>
                            <label class="text-sm text-gray-600">Slug</label>
                            <input type="text" name="slug" x-model="form.slug"
                                class="w-full border-gray-300 rounded-md text-sm px-2 py-1.5 focus:ring-blue-300"
                                required>
                        </div>
                    </template>

                    <div>
                        <label class="text-sm text-gray-600">Mô tả</label>
                        <textarea name="description" rows="2" x-model="form.description"
                            class="w-full border-gray-300 rounded-md text-sm px-2 py-1.5 focus:ring-blue-300"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="openModal=false"
                            class="px-3 py-1.5 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <span x-text="editMode ? 'Cập nhật' : 'Thêm mới'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
