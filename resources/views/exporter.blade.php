@extends('base')

@section('nav')
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="/exporter">
                        <span data-feather="home"></span>
                        Export History<span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/user/profile')}}">
                        <span data-feather="user"></span>
                        User Profile
                    </a>
                </li>
            </ul>
        </div>
    </nav>
@endsection

@section('content')
    <table class="table table-bordered" id="exports-table">
        <thead>
        <tr>
            <th>Search ID</th>
            <th>Query</th>
            <th>Download TSV</th>
            <th>Created At</th>
        </tr>
        </thead>
    </table>
@endsection

@section('scripts')
    <!-- DataTables -->
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            $('#exports-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('datatables.exports') !!}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'search_query', name: 'search_query'},
                    {data: 'file', name: 'file'},
                    {data: 'created_at', name: 'created_at'}
                ]
            });
        });
    </script>
@endsection
