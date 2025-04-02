@extends('admin.layouts.app')

@section('title', 'إدارة القصص الإسلامية')

@section('styles')
  <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
  <style>
    :root {
      --islamic-green: #1a472a;
      --islamic-gold: #d4af37;
      --parchment: #f8f3e6;
    }

    .islamic-admin-header {
      background: linear-gradient(135deg, var(--islamic-green), #0d2e1c);
      border-bottom: 3px solid var(--islamic-gold);
      color: white;
      font-family: 'Amiri', serif;
      padding: 1.5rem;
      border-radius: 8px 8px 0 0;
      position: relative;
      overflow: hidden;
    }

    .islamic-admin-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('/images/arabesque-pattern.png') repeat;
      opacity: 0.1;
    }

    .islamic-admin-card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      background: white;
    }

    .islamic-btn-add {
      background: linear-gradient(to right, var(--islamic-green), #0d2e1c);
      border: none;
      color: white;
      padding: 0.5rem 1.5rem;
      font-family: 'Amiri', serif;
      font-size: 1rem;
      border-radius: 50px;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(26, 71, 42, 0.2);
    }

    .islamic-btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(26, 71, 42, 0.3);
      color: white;
    }

    .table th {
      background-color: var(--islamic-green);
      color: white;
      font-family: 'Amiri', serif;
    }

    .table td {
      font-family: 'Amiri', serif;
      vertical-align: middle;
    }

    .story-image {
      width: 80px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #e0d6c2;
    }

    .badge-category {
      background-color: rgba(212, 175, 55, 0.2);
      color: var(--islamic-green);
      font-family: 'Amiri', serif;
      padding: 0.35em 0.65em;
      border-radius: 50px;
      border: 1px solid var(--islamic-gold);
    }

    .featured-badge {
      background-color: rgba(212, 175, 55, 0.8);
      color: white;
    }

    .action-btns .btn {
      width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 3px;
    }

    .pagination .page-item.active .page-link {
      background-color: var(--islamic-green);
      border-color: var(--islamic-green);
    }

    .pagination .page-link {
      color: var(--islamic-green);
      font-family: 'Amiri', serif;
    }

    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 0.375rem 0.75rem;
      font-family: 'Amiri', serif;
    }

    @media (max-width: 768px) {
      .table-responsive {
        border: none;
      }

      .action-btns {
        display: flex;
        flex-direction: column;
        gap: 5px;
      }

      .action-btns .btn {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
      }
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid px-4 py-4">
    <div class="islamic-admin-card mb-5">
      <div class="islamic-admin-header d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0">
          <i class="fas fa-book-quran me-2"></i> إدارة القصص الإسلامية
        </h2>
        {{-- <a href="{{ route('admin.stories.create') }}" class="btn islamic-btn-add">
          <i class="fas fa-plus me-1"></i>اضافة قصة جديدة
        </a> --}}
      </div>
      <div class="row">
        <div class="col-md-12">
        </div>
        <a href="{{ route('admin.stories.create') }}" class="btn islamic-btn-add">
          <i class="fas fa-plus me-1"></i>اضافة قصة جديدة
        </a>
      </div>
      <div class="card-body">
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-right: 5px solid var(--islamic-gold);">
            <div class="d-flex align-items-center">
              <i class="fas fa-check-circle me-2"></i>
              <div style="font-family: 'Amiri', serif;">
                {{ session('success') }}
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-hover align-middle" id="storiesTable">
            <thead>
              <tr>
                <th width="50">#</th>
                <th width="100">الصورة</th>
                <th>العنوان</th>
                <th>التصنيف</th>
                <th width="120">المشاهدات</th>
                <th width="100">مميزة</th>
                <th width="150">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @forelse($stories as $story)
                <tr>
                  <td>{{ $story->id }}</td>
                  <td>
                    @if ($story->image_url)
                      <img src="{{ $story->image_url }}" alt="{{ $story->title }}" class="story-image">
                    @else
                      <span class="badge bg-secondary" style="font-family: 'Amiri', serif;">بدون صورة</span>
                    @endif
                  </td>
                  <td>
                    <div class="fw-bold" style="font-family: 'Amiri', serif;">{{ $story->title }}</div>
                    <small class="text-muted"
                      style="font-family: 'Amiri', serif;">{{ Str::limit($story->description, 50) }}</small>
                  </td>
                  <td>
                    <span class="badge badge-category">{{ $story->category }}</span>
                  </td>
                  <td>
                    <span style="font-family: 'Amiri', serif;">{{ number_format($story->view_count) }}</span>
                  </td>
                  <td>
                    {{-- <form action="{{ route('admin.stories.toggle-featured', $story->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $story->featured ? 'featured-badge' : 'bg-light' }}"
                                            style="border: 1px solid var(--islamic-gold); font-family: 'Amiri', serif;">
                                        {{ $story->featured ? 'مميزة' : 'عادية' }}
                                    </button>
                                </form> --}}
                  </td>
                  <td class="action-btns">
                    <a href="{{ route('stories.show', $story->id) }}" class="btn btn-info btn-sm" target="_blank"
                      title="عرض">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.stories.edit', $story->id) }}" class="btn btn-primary btn-sm"
                      title="تعديل">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.stories.destroy', $story->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه القصة؟');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4" style="font-family: 'Amiri', serif;">
                    <i class="fas fa-book-open fa-2x mb-3 d-block" style="color: #ddd;"></i>
                    لا توجد قصص مضافة حتى الآن
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($stories->hasPages())
          <div class="mt-4">
            {{ $stories->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#storiesTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ar.json'
        },
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        pageLength: 25,
        responsive: true,
        order: [
          [0, 'desc']
        ],
        columnDefs: [{
          orderable: false,
          targets: [1, 5, 6]
        }]
      });
    });
  </script>
@endpush
