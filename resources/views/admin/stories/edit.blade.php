@extends('admin.layouts.app')

@section('title', 'تعديل القصة')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --islamic-green: #1a472a;
        --islamic-gold: #d4af37;
        --parchment: #f8f3e6;
    }

    .islamic-edit-header {
        background: linear-gradient(135deg, var(--islamic-green), #0d2e1c);
        border-bottom: 3px solid var(--islamic-gold);
        color: white;
        font-family: 'Amiri', serif;
        padding: 1.5rem;
        border-radius: 8px 8px 0 0;
        position: relative;
        overflow: hidden;
    }

    .islamic-edit-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('/images/arabesque-pattern.png') repeat;
        opacity: 0.1;
    }

    .islamic-edit-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        background: white;
    }

    .islamic-edit-label {
        font-family: 'Amiri', serif;
        font-size: 1.1rem;
        color: var(--islamic-green);
        margin-bottom: 0.5rem;
        display: block;
    }

    .islamic-edit-control {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        font-family: 'Amiri', serif;
        transition: all 0.3s;
    }

    .islamic-edit-control:focus {
        border-color: var(--islamic-gold);
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .islamic-btn-update {
        background: linear-gradient(to right, var(--islamic-green), #0d2e1c);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-family: 'Amiri', serif;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(26, 71, 42, 0.2);
    }

    .islamic-btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 71, 42, 0.3);
        color: white;
    }

    .islamic-sidebar-card {
        background: var(--parchment);
        border: 1px solid #e0d6c2;
        border-radius: 8px;
        overflow: hidden;
    }

    .islamic-sidebar-header {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.8), rgba(212, 175, 55, 0.6));
        color: var(--islamic-green);
        font-family: 'Amiri', serif;
        padding: 1rem;
        border-bottom: 1px solid #e0d6c2;
    }

    .image-preview-container {
        border: 2px dashed #ddd;
        border-radius: 8px;
        background: var(--parchment);
        padding: 1rem;
        text-align: center;
        position: relative;
        min-height: 200px;
    }

    .image-preview-container.has-image {
        border-color: var(--islamic-gold);
    }

    #image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 6px;
        border: 1px solid #e0d6c2;
    }

    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        font-family: 'Amiri', serif;
        color: #555;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }

    .note-editor.note-frame {
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .islamic-checkbox {
        position: relative;
        padding-right: 2rem;
    }

    .islamic-checkbox input {
        position: absolute;
        opacity: 0;
    }

    .islamic-checkbox label {
        position: relative;
        cursor: pointer;
        font-family: 'Amiri', serif;
        color: var(--islamic-green);
    }

    .islamic-checkbox label:before {
        content: '';
        position: absolute;
        right: -2rem;
        top: 0;
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid var(--islamic-gold);
        border-radius: 4px;
        background: white;
    }

    .islamic-checkbox input:checked + label:after {
        content: '✓';
        position: absolute;
        right: -1.8rem;
        top: -0.1rem;
        color: var(--islamic-green);
        font-size: 1.2rem;
    }

    .islamic-meta-info {
        background: rgba(212, 175, 55, 0.1);
        border-right: 3px solid var(--islamic-gold);
        border-radius: 6px;
        padding: 1rem;
        font-family: 'Amiri', serif;
    }

    @media (max-width: 768px) {
        .islamic-edit-header h2 {
            font-size: 1.5rem;
        }

        .islamic-edit-control {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="font-family: 'Amiri', serif; color: var(--islamic-green);">
            <i class="fas fa-edit me-2"></i> تعديل قصة: {{ $story->title }}
        </h1>
        <a href="{{ route('admin.stories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> رجوع
        </a>
    </div>

    <div class="islamic-edit-card mb-5">
        <div class="islamic-edit-header">
            <h2 class="h4 mb-0">
                <i class="fas fa-pen-fancy me-2"></i> تعديل بيانات القصة
            </h2>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.stories.update', $story->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-right: 5px solid var(--islamic-gold);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            <h6 class="mb-1" style="font-family: 'Amiri', serif;">حدثت الأخطاء التالية:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label for="title" class="islamic-edit-label">
                                <i class="fas fa-heading me-1"></i> عنوان القصة <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control islamic-edit-control" id="title" name="title"
                                   value="{{ old('title', $story->title) }}" required placeholder="أدخل عنوان القصة">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="islamic-edit-label">
                                <i class="fas fa-align-left me-1"></i> الوصف المختصر <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control islamic-edit-control" id="description" name="description"
                                      rows="3" required placeholder="أدخل وصفاً مختصراً للقصة">{{ old('description', $story->description) }}</textarea>
                            <small class="text-muted" style="font-family: 'Amiri', serif;">وصف موجز يظهر في صفحة القائمة (50-150 حرف)</small>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="islamic-edit-label">
                                <i class="fas fa-book me-1"></i> محتوى القصة <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control islamic-edit-control" id="content" name="content"
                                      placeholder="اكتب محتوى القصة هنا...">{{ old('content', $story->content) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="source" class="islamic-edit-label">
                                        <i class="fas fa-quote-right me-1"></i> المصدر
                                    </label>
                                    <input type="text" class="form-control islamic-edit-control" id="source"
                                           name="source" value="{{ old('source', $story->source) }}" placeholder="مثال: سورة الكهف، صحيح البخاري">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="video_url" class="islamic-edit-label">
                                        <i class="fas fa-video me-1"></i> رابط الفيديو
                                    </label>
                                    <input type="url" class="form-control islamic-edit-control" id="video_url"
                                           name="video_url" value="{{ old('video_url', $story->video_url) }}" placeholder="رابط YouTube أو Vimeo">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="islamic-sidebar-card mb-4">
                            <div class="islamic-sidebar-header">
                                <i class="fas fa-tags me-1"></i> التصنيف والمعلومات
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label for="category" class="islamic-edit-label">
                                        تصنيف القصة <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select islamic-edit-control" id="category" name="category" required>
                                        <option value="">اختر التصنيف</option>
                                        <option value="قصص الأنبياء" {{ old('category', $story->category) == 'قصص الأنبياء' ? 'selected' : '' }}>قصص الأنبياء</option>
                                        <option value="قصص القرآن" {{ old('category', $story->category) == 'قصص القرآن' ? 'selected' : '' }}>قصص القرآن</option>
                                        <option value="قصص الصحابة" {{ old('category', $story->category) == 'قصص الصحابة' ? 'selected' : '' }}>قصص الصحابة</option>
                                        <option value="قصص السيرة" {{ old('category', $story->category) == 'قصص السيرة' ? 'selected' : '' }}>قصص السيرة</option>
                                        <option value="دروس وعبر" {{ old('category', $story->category) == 'دروس وعبر' ? 'selected' : '' }}>دروس وعبر</option>
                                        @if(!in_array($story->category, ['قصص الأنبياء', 'قصص القرآن', 'قصص الصحابة', 'قصص السيرة', 'دروس وعبر']))
                                            <option value="{{ $story->category }}" selected>{{ $story->category }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <div class="islamic-checkbox">
                                        <input type="checkbox" id="featured" name="featured" value="1" {{ old('featured', $story->featured) ? 'checked' : '' }}>
                                        <label for="featured">
                                            <i class="fas fa-star me-1"></i> قصة مميزة
                                        </label>
                                    </div>
                                    <small class="text-muted" style="font-family: 'Amiri', serif;">سيظهر في القسم المميز بالصفحة الرئيسية</small>
                                </div>

                                <div class="islamic-meta-info mt-4">
                                    <h6 class="mb-3" style="font-family: 'Amiri', serif; color: var(--islamic-green);">
                                        <i class="fas fa-info-circle me-2"></i> معلومات القصة
                                    </h6>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span style="font-family: 'Amiri', serif;">تاريخ الإضافة:</span>
                                        {{ $story->created_at->format('Y-m-d') }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        <span style="font-family: 'Amiri', serif;">آخر تحديث:</span>
                                        {{ $story->updated_at->format('Y-m-d') }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-eye me-2"></i>
                                        <span style="font-family: 'Amiri', serif;">المشاهدات:</span>
                                        {{ $story->view_count }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="islamic-sidebar-card">
                            <div class="islamic-sidebar-header">
                                <i class="fas fa-image me-1"></i> صورة القصة
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="image" class="islamic-edit-label">
                                        تغيير صورة القصة
                                    </label>
                                    <input class="form-control islamic-edit-control" type="file"
                                           id="image" name="image" accept="image/*" onchange="previewImage(this);">
                                    <small class="text-muted" style="font-family: 'Amiri', serif;">الصيغ المسموحة: JPG, PNG (الحد الأقصى: 2MB)</small>
                                </div>

                                <div class="image-preview-container mt-3 {{ $story->image_url ? 'has-image' : '' }}">
                                    @if($story->image_url)
                                        <img id="image-preview" src="{{ $story->image_url }}" alt="صورة القصة الحالية" class="img-fluid">
                                        <small class="d-block mt-2" style="font-family: 'Amiri', serif;">الصورة الحالية</small>
                                    @else
                                        <div class="text-muted" style="font-family: 'Amiri', serif;">
                                            <i class="fas fa-image fa-3x mb-2 d-block"></i>
                                            لا توجد صورة حالية
                                        </div>
                                        <img id="image-preview" src="#" alt="معاينة الصورة الجديدة" class="img-fluid d-none">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-top text-center">
                    <button type="submit" class="btn islamic-btn-update me-2">
                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i> إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<script>
    $(document).ready(function() {
        // Initialize rich text editor with Arabic support
        $('#content').summernote({
            placeholder: 'اكتب محتوى القصة هنا...',
            height: 400,
            lang: 'ar-AR',
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video', 'table', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0]);
                }
            }
        });

        // Initialize select2 with Arabic
        $('#category').select2({
            placeholder: "اختر تصنيف القصة",
            language: "ar",
            allowClear: true,
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            }
        });

        // Function to upload image
        function uploadImage(file) {
            // Here you would typically upload to server
            // For demo, we'll just create a local URL
            var reader = new FileReader();
            reader.onloadend = function() {
                var image = $('<img>').attr('src', reader.result);
                $('#content').summernote('insertNode', image[0]);
            };
            reader.readAsDataURL(file);
        }
    });

    // Image preview function
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('d-none');
                $('.image-preview-container').addClass('has-image');
                $('.image-preview-container .text-muted').hide();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
