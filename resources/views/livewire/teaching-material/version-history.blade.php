<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 mb-2">
            <a href="{{ route('teaching-materials.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Kembali
            </a>
            <span class="text-gray-400">|</span>
            <h1 class="text-2xl font-bold text-gray-800">📜 Version History</h1>
        </div>
        <p class="text-gray-600">Track semua versi dari: <strong>{{ $material->title }}</strong></p>
    </div>

    <!-- Current Material Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 mb-2">Current Material</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p><strong>Title:</strong> {{ $material->title }}</p>
                    <p><strong>Version:</strong> v{{ $material->version_number }}</p>
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 text-xs rounded 
                            @if($material->status === 'approved') bg-green-100 text-green-800
                            @elseif($material->status === 'pending_approval') bg-yellow-100 text-yellow-800
                            @elseif($material->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $material->status_label }}
                        </span>
                    </p>
                    <p><strong>Total Versions:</strong> {{ $versions->count() }} versi</p>
                </div>
            </div>
            <a href="{{ route('teaching-materials.show', $material->id) }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                👁️ Lihat Material
            </a>
        </div>
    </div>

    <!-- Version Timeline -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">📊 Version Timeline</h2>
        
        <div class="space-y-4">
            @foreach($versions as $index => $version)
                <div class="relative pl-8 pb-4 
                    @if(!$loop->last) border-l-2 border-gray-300 @endif">
                    
                    <!-- Version Circle -->
                    <div class="absolute left-0 top-0 w-6 h-6 rounded-full flex items-center justify-center
                        @if($version->id === $material->id) bg-blue-600 ring-4 ring-blue-100
                        @elseif($version->status === 'approved') bg-green-600
                        @elseif($version->status === 'pending_approval') bg-yellow-600
                        @elseif($version->status === 'rejected') bg-red-600
                        @else bg-gray-600
                        @endif">
                        <span class="text-white text-xs font-bold">{{ $version->version_number }}</span>
                    </div>

                    <!-- Version Card -->
                    <div class="bg-gray-50 rounded-lg p-4 border 
                        @if($version->id === $material->id) border-blue-500 ring-2 ring-blue-100 @else border-gray-200 @endif">
                        
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="font-semibold text-gray-800">
                                        Version {{ $version->version_number }}
                                        @if($version->id === $material->id)
                                            <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded">Current</span>
                                        @endif
                                    </h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded 
                                        @if($version->status === 'approved') bg-green-100 text-green-800
                                        @elseif($version->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                        @elseif($version->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $version->status_label }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><strong>Created:</strong> {{ $version->created_at->format('d M Y H:i') }} 
                                        ({{ $version->created_at->diffForHumans() }})
                                    </p>
                                    <p><strong>By:</strong> {{ $version->creator->name }}</p>
                                    
                                    @if($version->revision_notes)
                                        <p><strong>Notes:</strong> {{ $version->revision_notes }}</p>
                                    @endif
                                    
                                    <p><strong>Stats:</strong> 👁️ {{ $version->view_count }} views • ⬇️ {{ $version->download_count }} downloads</p>
                                    
                                    @if($version->attachments->count() > 0)
                                        <p><strong>Attachments:</strong> {{ $version->attachments->count() }} files</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('teaching-materials.show', $version->id) }}" 
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded text-center transition">
                                    👁️ View
                                </a>
                                
                                @if($index < $versions->count() - 1)
                                    <button 
                                        wire:click="openCompareModal({{ $version->id }}, {{ $versions[$index + 1]->id }})"
                                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded transition">
                                        🔄 Compare
                                    </button>
                                @endif
                                
                                @if($version->id !== $material->id && ($version->created_by === auth()->id() || auth()->user()->canManageUsers()))
                                    <button 
                                        wire:click="restoreVersion({{ $version->id }})"
                                        wire:confirm="Restore version {{ $version->version_number }}? Material ini akan di-clone ke Draft baru."
                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition">
                                        ↩️ Restore
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Comparison Modal -->
    @if($showCompareModal && $compareVersion1 && $compareVersion2)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">
                        🔄 Compare Versions: v{{ $compareVersion1->version_number }} vs v{{ $compareVersion2->version_number }}
                    </h3>
                    <button 
                        wire:click="closeCompareModal"
                        class="text-gray-600 hover:text-gray-800 text-2xl font-bold">
                        ×
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="space-y-4">
                        @foreach($comparisonData as $field => $data)
                            <div class="border rounded-lg p-4 
                                @if($data['changed']) border-yellow-500 bg-yellow-50 @else border-gray-200 @endif">
                                
                                <div class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <span class="capitalize">{{ str_replace('_', ' ', $field) }}</span>
                                    @if($data['changed'])
                                        <span class="ml-2 px-2 py-0.5 bg-yellow-600 text-white text-xs rounded">Changed</span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Version 1 -->
                                    <div class="bg-white rounded p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">v{{ $compareVersion1->version_number }}</p>
                                        <p class="text-sm text-gray-800 break-words">{{ $data['v1'] }}</p>
                                    </div>

                                    <!-- Version 2 -->
                                    <div class="bg-white rounded p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">v{{ $compareVersion2->version_number }}</p>
                                        <p class="text-sm text-gray-800 break-words">{{ $data['v2'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Summary -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Summary:</strong> 
                                {{ collect($comparisonData)->where('changed', true)->count() }} field(s) changed 
                                out of {{ count($comparisonData) }} total fields.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end">
                    <button 
                        wire:click="closeCompareModal"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
