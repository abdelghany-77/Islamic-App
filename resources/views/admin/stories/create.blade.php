@extends('admin.layouts.app')

@section('title', 'إضافة قصة جديدة')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    :root {
      --islamic-green: #1a472a;
      --islamic-gold: #d4af37;
      --parchment: #f8f3e6;
    }

    body {
      background: #f5f5f5 url('/images/admin-bg-pattern.png');
    }

    .islamic-form-header {
      background: linear-gradient(135deg, var(--islamic-green), #0d2e1c);
      border-bottom: 3px solid var(--islamic-gold);
      color: white;
      font-family: 'Amiri', serif;
      padding: 1.5rem;
      border-radius: 8px 8px 0 0;
      position: relative;
      overflow: hidden;
    }

    .islamic-form-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('/images/arabesque-pattern.png') repeat;
      opacity: 0.1;
    }

    .islamic-form-card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      background: white;
    }

    .islamic-form-label {
      font-family: 'Amiri', serif;
      font-size: 1.1rem;
      color: var(--islamic-green);
      margin-bottom: 0.5rem;
      display: block;
    }

    .islamic-form-control {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 0.75rem 1rem;
      font-family: 'Amiri', serif;
      transition: all 0.3s;
    }

    .islamic-form-control:focus {
      border-color: var(--islamic-gold);
      box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .islamic-btn-primary {
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

    .islamic-btn-primary:hover {
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

    .image-preview-placeholder {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: #999;
      font-family: 'Amiri', serif;
    }

    #image-preview {
      max-width: 100%;
      max-height: 300px;
      border-radius: 6px;
      display: none;
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

    .note-editor.note-frame .note-toolbar {
      background: #f8f9fa;
      border-bottom: 1px solid #eee;
    }

    .note-editor.note-frame .note-statusbar {
      background: #f8f9fa;
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

    .islamic-checkbox input:checked+label:after {
      content: '✓';
      position: absolute;
      right: -1.8rem;
      top: -0.1rem;
      color: var(--islamic-green);
      font-size: 1.2rem;
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0" style="font-family: 'Amiri', serif; color: var(--islamic-green);">
        <i class="fas fa-book-open me-2"></i> إضافة قصة جديدة
      </h1>
      <a href="{{ route('admin.stories.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> رجوع
      </a>
    </div>

    <div class="islamic-form-card mb-5">
      <div class="islamic-form-header">
        <h2 class="h4 mb-0">
          <i class="fas fa-pen-fancy me-2"></i> بيانات القصة الإسلامية
        </h2>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert"
              style="border-right: 5px solid var(--islamic-gold);">
              <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>
                  <h6 class="mb-1" style="font-family: 'Amiri', serif;">حدثت الأخطاء التالية:</h6>
                  <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
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
                <label for="title" class="islamic-form-label">
                  <i class="fas fa-heading me-1"></i> عنوان القصة <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control islamic-form-control" id="title" name="title"
                  value="{{ old('title') }}" required placeholder="أدخل عنوان القصة">
              </div>

              <div class="mb-4">
                <label for="description" class="islamic-form-label">
                  <i class="fas fa-align-left me-1"></i> الوصف المختصر <span class="text-danger">*</span>
                </label>
                <textarea class="form-control islamic-form-control" id="description" name="description" rows="3" required
                  placeholder="أدخل وصفاً مختصراً للقصة">{{ old('description') }}</textarea>
                <small class="text-muted" style="font-family: 'Amiri', serif;">وصف موجز يظهر في صفحة القائمة (50-150
                  حرف)</small>
              </div>

              <div class="mb-4">
                <label for="content" class="islamic-form-label">
                  <i class="fas fa-book me-1"></i> محتوى القصة <span class="text-danger">*</span>
                </label>
                <textarea class="form-control islamic-form-control" id="content" name="content" placeholder="اكتب محتوى القصة هنا...">{{ old('content') }}</textarea>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-4">
                    <label for="source" class="islamic-form-label">
                      <i class="fas fa-quote-right me-1"></i> المصدر
                    </label>
                    <input type="text" class="form-control islamic-form-control" id="source" name="source"
                      value="{{ old('source') }}" placeholder="مثال: سورة الكهف، صحيح البخاري">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-4">
                    <label for="video_url" class="islamic-form-label">
                      <i class="fas fa-video me-1"></i> رابط الفيديو
                    </label>
                    <input type="url" class="form-control islamic-form-control" id="video_url" name="video_url"
                      value="{{ old('video_url') }}" placeholder="رابط YouTube أو Vimeo">
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
                    <label for="category" class="islamic-form-label">
                      تصنيف القصة <span class="text-danger">*</span>
                    </label>
                    <select class="form-select islamic-form-control" id="category" name="category" required>
                      <option value="">اختر التصنيف</option>
                      <option value="قصص الأنبياء" {{ old('category') == 'قصص الأنبياء' ? 'selected' : '' }}>قصص الأنبياء
                      </option>
                      <option value="قصص القرآن" {{ old('category') == 'قصص القرآن' ? 'selected' : '' }}>قصص القرآن
                      </option>
                      <option value="قصص الصحابة" {{ old('category') == 'قصص الصحابة' ? 'selected' : '' }}>قصص الصحابة
                      </option>
                      <option value="قصص السيرة" {{ old('category') == 'قصص السيرة' ? 'selected' : '' }}>قصص السيرة
                      </option>
                      <option value="دروس وعبر" {{ old('category') == 'دروس وعبر' ? 'selected' : '' }}>دروس وعبر
                      </option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <div class="islamic-checkbox">
                      <input type="checkbox" id="featured" name="featured" value="1"
                        {{ old('featured') ? 'checked' : '' }}>
                      <label for="featured">
                        <i class="fas fa-star me-1"></i> قصة مميزة
                      </label>
                    </div>
                    <small class="text-muted" style="font-family: 'Amiri', serif;">سيظهر في القسم المميز بالصفحة
                      الرئيسية</small>
                  </div>
                </div>
              </div>

              <div class="islamic-sidebar-card">
                <div class="islamic-sidebar-header">
                  <i class="fas fa-image me-1"></i> صورة القصة
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="image" class="islamic-form-label">
                      اختر صورة للقصة
                    </label>
                    <input class="form-control islamic-form-control" type="file" id="image" name="image"
                      accept="image/*" onchange="previewImage(this);">
                    <small class="text-muted" style="font-family: 'Amiri', serif;">الصيغ المسموحة: JPG, PNG (الحد
                      الأقصى: 2MB)</small>
                  </div>

                  <div class="image-preview-container mt-3">
                    <div class="image-preview-placeholder">
                      <i class="fas fa-image fa-3x mb-2 d-block text-muted"></i>
                      <span style="font-family: 'Amiri', serif;">معاينة الصورة</span>
                    </div>
                    <img id="image-preview" src="#" alt="معاينة الصورة" class="img-fluid">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-5 pt-3 border-top text-center">
            <button type="submit" class="btn islamic-btn-primary me-2">
              <i class="fas fa-save me-1"></i> حفظ القصة
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
        createTag: function(params) {
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
          $('#image-preview').attr('src', e.target.result).show();
          $('.image-preview-placeholder').hide();
          $('.image-preview-container').addClass('has-image');
        }

        reader.readAsDataURL(input.files[0]);
      } else {
        $('#image-preview').hide();
        $('.image-preview-placeholder').show();
        $('.image-preview-container').removeClass('has-image');
      }
    }
  </script>
@endpush
