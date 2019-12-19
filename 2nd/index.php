<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>

<div class="container mt-3" id="app">
    <div class="card mb-3">
        <h5 class="card-header">Upload new CSV file</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <input class="form-control w-100" type="file" id="file" ref="file">
                </div>
                <div class="col-4">
                    <button class="btn btn-primary w-100" type="button" @click='uploadFile()'>Upload file</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3" v-if="result">
        <h5 class="card-header">Result table</h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th v-for="head in result.header">{{head}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in result.data">
                            <td v-for="item in item">{{item}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <h5>Add new row</h5>
            </div>
        </div>
    </div>

    <div class="card mb-3" v-if="result">
        <h5 class="card-header">Json respnse</h5>
        <div class="card-body">
            <p class="text-monospace">
                {{result}}
            </p>
        </div>
    </div>
</div>

<!-- Connect Vue and Axios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-axios@2.1.5/dist/vue-axios.min.js"></script>

<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                file: '',
                result: null,
            }
        },
        methods: {
            uploadFile() {

                this.file = this.$refs.file.files[0];

                let formData = new FormData();
                formData.append('file', this.file);

                let vueInstance = this; //This line is important

                axios.post('/table/upload.php', formData, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(function (response) {
                        vueInstance.result = response.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
    })
</script>
</body>
</html>