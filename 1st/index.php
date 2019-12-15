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
    <div class="card">
        <h5 class="card-header">Add new task</h5>
        <div class="card-body">
            <form @submit.prevent="addTask">
                <div class="row">
                    <div class="col-8">
                        <input v-model="newTask.name" required="true" placeholder="Task name" class="form-control">
                    </div>
                    <div class="col-2">
                        <vuejs-datepicker v-model="newTask.date" :required="true" input-class="form-control bg-white"
                                          placeholder="Date"></vuejs-datepicker>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary w-100">Add task</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <h5 class="card-header">Response</h5>
        <div class="card-body">
            <p class="text-monospace">
            {{jsonResponse}}
            </p>
        </div>
    </div>
</div>

<!-- Connect date picker -->
<script src="https://unpkg.com/vue"></script>
<script src="https://unpkg.com/vuejs-datepicker"></script>
<script>
    const app = new Vue({
        el: '#app',
        components: {
            vuejsDatepicker
        },
        data() {
            return {
                newTask: {
                    name: null,
                    date: null,
                },
                jsonResponse: null,
            }
        },
        methods: {
            addTask() {
                return axios.post('task.php', {
                    name: this.newTask.name,
                    date: this.newTask.date
                }).then(response => {
                    this.jsonResponse = response.data;
                })
            }
        }
    })
</script>

<!-- Connect Vue and Axios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-axios@2.1.5/dist/vue-axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</body>
</html>