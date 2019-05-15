<template>
  <div>
    <div class="container" v-if="$gate.isAdminOrAuthor()">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Users Table</h3>

              <div class="card-tools">
                <button class="btn btn-success" @click="newUser">
                  Add New &nbsp;
                  <i class="fas fa-user-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <tbody>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered At</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="user in users.data" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.type }}</td>
                    <td>{{ user.created_at | myDate }}</td>
                    <td>
                      <button class="btn btn-sm btn-warning" @click="editUser(user)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination :data="users" @pagination-change-page="getResults"></pagination>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- Modal -->
      <div
        class="modal fade"
        id="addUser"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addUserLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addUserLabel">{{ editmode ? 'Edit User': 'Add User' }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="editmode ? updateUser() : createUser()">
              <div class="modal-body">
                <div class="form-group">
                  <input
                    type="text"
                    v-model="form.name"
                    name="name"
                    class="form-control"
                    :class="{ 'is-invalid' : form.errors.has('name') }"
                    placeholder="Name"
                  >
                  <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                  <input
                    type="email"
                    v-model="form.email"
                    name="email"
                    class="form-control"
                    :class="{ 'is-invalid' : form.errors.has('email') }"
                    placeholder="Email Address"
                  >
                  <has-error :form="form" field="email"></has-error>
                </div>
                <div class="form-group">
                  <textarea
                    v-model="form.bio"
                    name="bio"
                    class="form-control"
                    :class="{ 'is-invalid' : form.errors.has('bio') }"
                    placeholder="Short bio for user (Optional)"
                  ></textarea>
                  <has-error :form="form" field="bio"></has-error>
                </div>
                <div class="form-group">
                  <select
                    v-model="form.type"
                    name="type"
                    class="form-control"
                    :class="{ 'is-invalid' : form.errors.has('type') }"
                  >
                    <option value>Select User Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">Member</option>
                    <option value="author">Author</option>
                  </select>
                  <has-error :form="form" field="type"></has-error>
                </div>
                <div class="form-group">
                  <input
                    type="password"
                    v-model="form.password"
                    name="password"
                    class="form-control"
                    :class="{ 'is-invalid' : form.errors.has('password') }"
                    placeholder="Password"
                  >
                  <has-error :form="form" field="password"></has-error>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">{{ editmode ? 'Update' : 'Create'}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div v-if="!$gate.isAdminOrAuthor()">
      <error-page></error-page>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        type: "",
        bio: "",
        photo: ""
      }),
      users: {},
      editmode: false
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();
      axios.get("api/user?page=" + page).then(response => {
        this.users = response.data;
        this.$Progress.finish();
      });
    },
    loadUsers() {
      if (this.$gate.isAdminOrAuthor()) {
        axios.get("/api/user").then(({ data }) => (this.users = data));
      }
    },
    createUser() {
      if (this.$gate.isAdminOrAuthor()) {
        this.$Progress.start();
        this.form
          .post("/api/user")
          .then(() => {
            Fire.$emit("AfterComplete");
            $("#addUser").modal("hide");
            this.$Progress.finish();
            Toast.fire({
              type: "success",
              title: "User created successfully"
            });
          })
          .catch(() => {
            this.$Progress.fail();
            Toast.fire({
              type: "error",
              title: "User not created"
            });
          });
      }
    },
    updateUser() {
      this.$Progress.start();
      this.form
        .put("/api/user/" + this.form.id)
        .then(({ data }) => {
          Fire.$emit("AfterComplete");
          $("#addUser").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: "User edited successfully"
          });
        })
        .catch(() => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: "User not edited"
          });
        });
    },
    deleteUser(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          this.form
            .delete("/api/user/" + id)
            .then(() => {
              this.$Progress.finish();
              Swal.fire("Deleted!", "Your data has been deleted.", "success");
              Fire.$emit("AfterComplete");
            })
            .catch(() => {
              this.$Progress.fail();
              Swal.fire({
                type: "error",
                title: "Oops...",
                text: "Something went wrong!"
              });
            });
        }
      });
    },
    newUser() {
      this.editmode = false;
      this.form.reset();
      $("#addUser").modal("show");
    },
    editUser(user) {
      this.editmode = true;
      this.form.reset();
      $("#addUser").modal("show");
      this.form.fill(user);
    }
  },
  mounted() {
    this.$Progress.finish();
  },
  created() {
    this.loadUsers();
    Fire.$on("AfterComplete", () => {
      this.loadUsers();
    });

    Fire.$on("searching", () => {
      let query = this.$parent.search;
      axios
        .get("/api/finduser?q=" + query)
        .then(data => {
          this.users = data.data;
        })
        .catch(() => {});
    });
    // setInterval(() => this.loadUsers(), 3000);

    // Progress bar
    //  [App.vue specific] When App.vue is first loaded start the progress bar
    this.$Progress.start();
    //  hook the progress bar to start before we move router-view
    this.$router.beforeEach((to, from, next) => {
      //  does the page we want to go to have a meta.progress object
      if (to.meta.progress !== undefined) {
        let meta = to.meta.progress;
        // parse meta tags
        this.$Progress.parseMeta(meta);
      }
      //  start the progress bar
      this.$Progress.start();
      //  continue to next page
      next();
    });
    //  hook the progress bar to finish after we've finished moving router-view
    this.$router.afterEach((to, from) => {
      //  finish the progress bar
      this.$Progress.finish();
    });
  }
};
</script>
