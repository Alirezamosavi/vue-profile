
<template>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-6">
        <div class="card card-default mt-3">
          <div class="card-header"><b>Update image Profile</b></div>

          <div class="card-body">
            <form>
              <div class="mb-3">
                <label for="disabledTextInput" class="form-label">image</label>

                <!-- by this input upload new image-->
                <input
                  type="file"
                  @change="uploadPhoto"
                  name="photo"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('photo') }"
                />
              </div>
              <!-- by this button submit new image -->
              <!-- add in submit $auth.user().id, because function now current user by this code and verry importan in vue if installed vuejs with auth -->
              <button
                type="submit"
                @click.prevent="login($auth.user().id)"
                class="btn btn-primary"
              >
                Submit
              </button>
            </form>
          </div>
          <div
            v-for="book in books"
            :key="book.id"
            style="display: inline-flex"
          >

          <!-- show if user has image profile -->
            <div class="mr-auto mb-auto" v-if="book.photo">
              <img
                :src="`img/public/${book.photo}`"
                class="nav-link rounded-circle"
                width="150px"
                height="150px"
              />
            </div>
            <!-- if user doesnt have image profile show another image -->
            <div v-else>
              <img
                :src="`img/public/default.jpg`"
                class="rounded-circle nav-link"
                width="150px"
                height="150px"
              />
            </div>

            <!-- this is new image thwt iploaded from system but not yet submit to log in database -->
            <div v-if="imageprevi" class="ml-auto upload">
              <img
                :src="imageprevi"
                class="nav-link rounded-circle"
                width="150px"
                height="150px"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      books: {},
      success: false,
      form: new Form({
        // email: "",
        // password: "",
        photo: "",
      }),

      imageprevi: "", // this is to image upload from input type= file
      image: null,
    };
  },
  mounted() {
    this.getResults(); // get image or another things of user
  },
  methods: {
    uploadPhoto(e) {
      // upload new file or image do by this code
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.photo = reader.result;
        };
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          this.imageprevi = e.target.result;

          $(".upload").show();
        };
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },

    login(id) {  // if submit new inform or update log in here
      this.form
        .post("../v1/auth/update/" + id)

        .then(() => {   // this is toaster
          Toast.fire({
            icon: "success",
            title: "File uploaded successfully",
          });
          $(".upload").hide();
          this.getResults();
         
        })

        .catch(() => {
          console.log("Error.....");
        });
    },

    getResults() {
      axios
        .get("../v1/auth/show")

        .then((response) => {
          this.books = response.data.data;
        });
    },
  },
};
</script>
