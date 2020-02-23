@extends('layouts.app')

@section('content')
<div id="scope">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Button to Open the Modal -->
            <input v-model=queryString type="text">
            <button id="searchTMDB" type="button" class="btn btn-primary" data-toggle="modal" data-target="newProfile" v-on:click="searchTMDB" autofocus>
                Search
            </button>

            @foreach($movies as $movie)
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body border text-center">
                            {{$movie->title}}
                        </div>
                    </div>
                </div>
        @endforeach

        <!-- Create client modal start -->
            <div class="modal" id="newProfile">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">New Profile</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Create client modal end -->

        </div>
    </div>
</div>
@endsection
@section('javascript')
    <script>
        new Vue({
            el:      '#scope',
            data:    {
                profileName:     '',
                queryString:'',
            },
            methods: {
                searchTMDB: function () {
                    $('#newProfile').modal('show');
                    let url = 'https://api.themoviedb.org/3/search/movie?api_key='+ '{{env('TMDB_API_KEY')}}' + '&query=' + this.queryString;
                    console.log(url);
                    axios.get(url, {
                    }).then(function (response) {
                        console.log(response.data.results);
                    }).catch(function (error) {
                        alert('error with axios');
                    });
                },
            },
        });

    </script>
@endsection
