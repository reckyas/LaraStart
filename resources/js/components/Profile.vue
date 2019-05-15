<style scoped>
.widget-user-header.text-white {
  height: 250px;
}
</style>

<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div
            class="widget-user-header text-white"
            style="background: url('./img/cover.jpg') center center;"
          >
            <h3 class="widget-user-username">{{ profile.name }}</h3>
            <h5 class="widget-user-desc">Web Designer</h5>
          </div>
          <div class="widget-user-image">
            <img class="img-circle" :src="'./img/profile/'+profile.photo" alt="User Avatar">
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">3,200</h5>
                  <span class="description-text">SALES</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">13,000</h5>
                  <span class="description-text">FOLLOWERS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4">
                <div class="description-block">
                  <h5 class="description-header">35</h5>
                  <span class="description-text">PRODUCTS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link" href="#activity" data-toggle="tab">Activity</a>
              </li>
              <li class="nav-item active show">
                <a class="nav-link" href="#settings" data-toggle="tab">Settings</a>
              </li>
            </ul>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane" id="activity"></div>
              <!-- /.tab-pane -->

              <div class="tab-pane active show" id="settings">
                <form class="form-horizontal" @submit.prevent="updateProfile">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-12">
                      <input
                        type="text"
                        class="form-control"
                        v-model="form.name"
                        name="name"
                        :class="{ 'is-invalid' : form.errors.has('name') }"
                        placeholder="Name"
                      >
                      <has-error :form="form" field="name"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-12">
                      <input
                        type="text"
                        class="form-control"
                        v-model="form.email"
                        name="email"
                        :class="{ 'is-invalid' : form.errors.has('email') }"
                        placeholder="Email"
                      >
                      <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                    <div class="col-sm-12">
                      <textarea
                        class="form-control"
                        id="inputExperience"
                        v-model="form.bio"
                        name="bio"
                        :class="{ 'is-invalid' : form.errors.has('bio') }"
                        placeholder="Experience"
                      ></textarea>
                      <has-error :form="form" field="bio"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputSkills" class="col-sm-2 control-label">Photo</label>

                    <div class="col-sm-12">
                      <input
                        type="file"
                        class="form-control"
                        :class="{ 'is-invalid' : form.errors.has('photo') }"
                        id="inputFile"
                        name="photo"
                        @change="updateImage"
                      >
                      <has-error :form="form" field="photo"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassport" class="col-sm-2 control-label">Passport</label>

                    <div class="col-sm-12">
                      <small>Passport (Biarkan kosong jika tidak diganti)</small>
                      <input
                        type="password"
                        class="form-control"
                        id="inputPassport"
                        v-model="form.password"
                        name="email"
                        :class="{ 'is-invalid' : form.errors.has('password') }"
                        placeholder="Password"
                      >
                      <has-error :form="form" field="password"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-12">
                      <button type="submit" class="btn btn-success">Update</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.card-body -->
        </div>
      </div>
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
      profile: {}
    };
  },
  methods: {
    loadProfile() {
      axios
        .get("/api/profile")
        .then(({ data }) => {
          this.profile = data;
        })
        .catch(e => {});
    },
    updateImage(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      function isFileImage(file) {
        const acceptedImageTypes = [
          "image/jpg",
          "image/gif",
          "image/jpeg",
          "image/png"
        ];

        return file && acceptedImageTypes.includes(file["type"]);
      }
      if (isFileImage(file)) {
        if (file["size"] < 2111775) {
          reader.onloadend = () => {
            // console.log("RESULT", reader.result);
            this.form.photo = reader.result;
          };
          reader.readAsDataURL(file);
        } else {
          Swal.fire({
            type: "error",
            title: "Oops...",
            text: "Ukuran file yang anda upload terlalu besar"
          });
        }
      } else {
        Swal.fire({
          type: "error",
          title: "Oops...",
          text: "Exstensi file yang anda upload tidak di perbolehkan"
        });
      }
    },
    updateProfile() {
      this.$Progress.start();
      if (this.form.password == "") {
        this.form.password = undefined;
      }
      this.form
        .put("/api/profile")
        .then(({ data }) => {
          console.log(data);
          Fire.$emit("AfterComplete");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: "Profile updated"
          });
        })
        .catch(e => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: "Profile failed to edit"
          });
        });
    }
  },
  mounted() {},
  created() {
    axios
      .get("/api/profile")
      .then(({ data }) => {
        this.form.fill(data);
      })
      .catch(e => {});

    this.loadProfile();
    Fire.$on("AfterComplete", () => {
      this.loadProfile();
    });
    console.log(this.profile);
  }
};
</script>
