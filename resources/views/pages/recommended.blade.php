@extends('layouts.app')

@section('content')
    <div id="scope" class="row">
        <div v-show="loaded" class="loader"></div>

        <div class="card col-md-3" v-for="item in items.results">
            <img class="card-img-top" :src="imageUrl + item.poster_path" alt="">
                <a href="#" class="btn btn-primary">Adicionar</a>
            <div class="card-body">
                <h5 class="card-title">@{{ item.original_title }}</h5>
                <p class="card-text">@{{ item.overview }}</p>
            </div>
        </div>

    </div>
@endsection
@section('javascript')
    <script>
        new Vue({
            el:      "#scope",
            data:    {
                items:    [],
                baseUrl:  'https://api.themoviedb.org/3',
                apiKey:   '{{ env('TMDB_API_KEY') }}',
                imageUrl: 'https://image.tmdb.org/t/p/w342',
                loaded:   true
            },
            created: function () {
                // Create the method you made below
                this.fetchData();
            },
            methods: {
                // Fetch data from the API
                fetchData: function () {
                    let vm = this;
                    let url = this.baseUrl + '/discover/movie?api_key=' + this.apiKey + '&sort_by=popularity.desc';
                    console.log(url);

                    // Make a request for a user with a given ID
                    axios.get(this.baseUrl + '/discover/movie?api_key=' + this.apiKey + '&sort_by=popularity.desc')
                        .then(function (response) {
                            // handle success
                            vm.items = response.data;
                            vm.loaded = false;

                        })
                        .catch(function (error) {
                            // handle error
                            console.log(error);
                        })
                        .then(function () {
                            // always executed
                        });
                }
            }
        });
    </script>
@endsection
