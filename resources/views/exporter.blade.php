@extends('base')

@section('top-nav')
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">ISRC Exporter</a>
        <form style="width: 100%" id="search" action="{{url('/search')}}" method="get">
            <input name="q" class="form-control form-control-dark w-100" type="text" placeholder="Search"
                   aria-label="Search">
        </form>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="{{url('/logout')}}">Sign out</a>
            </li>
        </ul>
    </nav>
@endsection

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
    <style type="text/css">
        .paginate_button {
            margin: 20px;
            cursor: pointer;
        }
    </style>
    <!-- sweetalert -->
    <script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- DataTables -->
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <script>
        var exports_table = $('#exports-table').DataTable({
            processing: true,
            serverSide: true,
            order: [[3, "desc"]],
            ajax: {
                url: '{!! route('datatables.exports') !!}',
                type: 'GET',
                dataSrc: function (json) {
                    try {
                        for (let i = 0; json.data.length; i++) {
                            let file = json.data[i].file;
                            if (json.data[i].job_complete == 1) {
                                json.data[i].file = '<a href="/export/' + file + '">' + file + '</a>'
                            }
                        }
                    }
                    catch (err) {
                        console.log(err.message);
                    }
                    return json.data;
                },
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'search_query', name: 'search_query'},
                {data: 'file', name: 'file'},
                {data: 'created_at', name: 'created_at'}
            ]
        });

        $('#search').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: form.attr('method'),
                url: url,
                data: form.serialize(),
                success: function (data) {
                    swal({
                        title: 'Job queued',
                        text: data.message,
                        icon: "success"
                    })
                        .then(() => {
                                exports_table.ajax.reload();
                            }
                        )
                }
            })
            ;
        })
        ;
    </script>
@endsection
