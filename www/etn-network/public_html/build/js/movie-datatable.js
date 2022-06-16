var tSearchTimout;
var movieDatatable;

var movieDatatableColumnDefinitions = {
    0: {
        'name': 'id',
        'filter': true,
        'filterType': 'text'
    },
    1: {
        'name': 'title',
        'filter': true,
        'filterType': 'text'
    },
    2: {
        'name': 'description',
        'filter': true,
        'filterType': 'text'
    }
    ,
    3: {
        'name': 'genre',
        'filter': true,
        'filterType': 'text'
    }
    ,
    4: {
        'name': 'release_date',
        'filter': true,
        'filterType': 'text'
    },
    5: {
        'name': 'tools',
        'filter': false,
        'filterType': null
    },
}

function rendermovieFilters(binitialLoad)
{
    
    var $movieDatatable = $('#movie-datatable');
    var $movieDatatableTHead = $movieDatatable.find('thead');

    if(!binitialLoad)
    {
        $movieDatatableTHead.find('tr:last').remove();
    }

    $movieDatatableTHead.append('<tr></tr>');
    $movieDatatable.dataTable().api().columns().every( function (i) {
        if(this.visible())
        {
            $movieDatatableTHead.find('tr:eq(1)').append('<th></th>');

            if(movieDatatableColumnDefinitions[i].filter)
            {
                var column = this;

                if(movieDatatableColumnDefinitions[i].filterType == 'text')
                {
                    var input = $('<input class="form-control" type="text" placeholder="Search ' + $(column.header()).text() + '" value="' + column.search() + '" />')
                        .appendTo( $movieDatatableTHead.find('tr:eq(1) th:last') )
                        .on( 'keyup change clear', function () {
                            var term = this.value;

                            clearTimeout(tSearchTimout);
                            tSearchTimout = setTimeout(function() { column.search(term, false, false ).draw(); }, 500);
                        } );
                }
                else
                {
                    var select = $('<select class="form-control"><option value="">Any</option></select>')
                        .appendTo( $movieDatatableTHead.find('tr:eq(1) th:last') )
                        .on( 'change', function () {
                            var term = this.value;

                            column.search(term, false, false ).draw();
                        } );

                    switch(i)
                    {
                        case 1:
                            if(column.search() == 'none')
                            {
                                select.append('<option value="none" selected="selected">None</option>');
                            } 
                            else 
                            {
                                select.append('<option value="none">None</option>');
                            }

                            for(var i = 0; i < chapters.length; i++)
                            {    
                                if(column.search() == chapters[i].urlName)
                                {
                                    select.append('<option value="' + chapters[i].urlName + '" selected="selected">' + chapters[i].name + '</option>');
                                } 
                                else 
                                {
                                    select.append('<option value="' + chapters[i].urlName + '">' + chapters[i].name + '</option>');
                                }
                            }

                            break;  
                    }

                }
            }
        }
    });
}

$(function(){
    movieDatatable = $('#movie-datatable').DataTable({
        initComplete: function() {  setTimeout('rendermovieFilters(true);', 50); },
        orderCellsTop: true,
        fixedHeader: true,
        bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem('movie_DataTables', JSON.stringify(oData) );
        },
        fnStateLoad: function (oSettings) {
            return JSON.parse( localStorage.getItem('movie_DataTables') );
        },
        dom: '<"top"lip<"clear">>rt<"bottom"ip<"clear">>',
        processing: true,
        serverSide: true,
        language: {
            info: 'Showing movie _START_ to _END_ of _TOTAL_ movies',
            paginate: {
                previous: '<i class="fal fa-chevron-left"></i>',
                next: '<i class="fal fa-chevron-right"></i>'
            },
            processing: '<img src="/images/loading.svg" class="dataTables_processing__loading" alt="Processing">'
        },
        lengthMenu: [ 20, 40, 50, 80, 100 ],
        order: [[ 1, "asc" ]],
        ajax: '/ajax/get-movie.html',
        columnDefs: [
            {
                targets: 0,
                name: "id",
                orderable: true,
                searchable : false,
                data: null,
                render: function ( data, type, row, meta ) {
                    return (data.id);
                }
            },
            {
                targets: 1,
                name: "title",
                orderable: true,
                searchable : false,
                data: 'title',
              
            },
            {
                targets: 2,
                name: "description",
                orderable: true,
                searchable : false,
                data: 'description'
            },
            {
                targets: 3,
                name: "genre",
                orderable: false,
                searchable : false,
                data: 'genre'
            },
            {
                targets: 4,
                name: "release_date",
                orderable: false,
                searchable : false,
                data: 'release_date'
            },
            {
                targets: 5,
                visible: true,
                orderable: false,
                searchable : false,
                data: null,
                className: 'datatable_actions',
                render: function ( data, type, row, meta ) {
                    return ('<a href="' + sSiteURL + 'edit.html?movie=' + data.code + '" class="edit-link"><i class="fal fa-pencil-alt"></i></a>&nbsp;&nbsp;<a href="' + sSiteURL + 'index.html?delete=' + data.code + '" class="red delete-link" onclick="return confirm(\'Are you sure you wish to delete ' + data.name + '?\');"><i class="fal fa-trash-alt"></i></a>') 
                }
            }
        ]
    });
    
    $('movie-datatable').on('column-visibility.dt', function() { rendermovieFilters(false); });


    var movieJSON = JSON.parse(localStorage.getItem('DataTables_movie-datatable_/movie/'));

    if(movieJSON != null)
    {
        movieJSON = movieJSON.columns;
        
        $('#movie-columns option').prop('selected', '');

        for(var i = 0; i < movieJSON.length; i++)
        {
            if(movieJSON[i].visible)
            {
                $('movie-columns option[value=\'' + movieDatatableColumnDefinitions[i].name + '\']').prop('selected', 'selected');
            }
        }

        $('movie-columns').multiselect('reload');
    }
});