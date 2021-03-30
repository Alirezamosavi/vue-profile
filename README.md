Adding User Profile  ( Vue + Laravel)

Video link
https://youtu.be/7eEexmDbrhw

1. install make image for Framework so then write in terminal 
  composer require intervention/image   ( i do that already because it take sometime but you do  )
  
2. add the below code in config/app.php

  Intervention\Image\ImageServiceProvider::class

  'Image' => Intervention\Image\Facades\Image::class  


3. after do that write the below code in terminal
   php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"

4. add the below code in the table of user

 $table->string('photo')->nullable(); // i set nullable, may user doesn`t wana have a image profile  

5. add the below codein Model of user

   protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo'
    ]; 
 
6. write in the terminal 
   php artisan migrate:rollback
   then
   php artisan migrate

7. till here we did every things that need to make image in laravel now ill make some things to make a real image 

8. add the below code in controller of userRegister
   public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3|confirmed',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        if($request->photo){  ///////////
            //  this code make image or convert file in controller and for play that i install require image in First
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/public/').$name); // this code add image that we upload in folder (public/img/public)
            $request->merge(['photo' => $name]);
           
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->photo = $request->photo; ///////////////////////////
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['status' => 'success'], 200);
    }

9. add the below code in controller of user
   use Illuminate\Support\Facades\Auth;
   
   public function index(Request $request)  // this function show data in Table for template
  {
    $user = Auth::user();   // for this play i must introduce currentUser to framework 
      return User::where('id', $user->id)->paginate();  // framework find all things of user in database and result show that in template 
  }	
  
  
  
10. now add all things need has for make image in frontend like VueJs

11. add the input or the below code in frontend in Register of template  


  <div class="form-group" v-bind:class="{ 'has-error': has_error && errors.password }">
                    <label for="password_confirmation">create image Profile</label>
                <input
                    type="file"
                    @change="uploadPhoto"
                    name="photo"
                    class="form-control"
                    
                  />                
                </div>
                
                <button type="submit" class="btn btn-primary">Submit</button>


form: new Form({
        photo: "",
      }),


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
        };
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
      register() {
        var app = this
        this.$auth.register({
          data: {
            name: app.name,
            email: app.email,
            photo:this.form.photo,
            password: app.password,
            password_confirmation: app.password_confirmation
          },
          success: function () {
            app.success = true
            this.$router.push({name: 'login', params: {successRegistrationRedirect: true}})
          },
          error: function (res) {
            // console.log(res.response.data.errors)
            app.has_error = true
            app.error = res.response.data.error
            app.errors = res.response.data.errors || {}
          }
        })
      }	

12. add the below code in menue, because we must make a where to show image profile of user

<ul class="navbar-nav ml-auto" v-if="$auth.check()">
        <li class="nav-item" style="">
          <router-link to="/update">
          <div class="" v-if="$auth.user().photo">
            
              <a
                href=""
                data-toggle="tooltip"
                data-placement="left"
                title="Update Profile"
              >
                <img
                  :src="`img/public/${$auth.user().photo}`"
                  class="rounded-circle nav-link image"
                  width="50px"
                  height="50px"
                />
              </a>
            <!-- </router-link> -->
          </div>
          <div v-else>
            <!-- <router-link to="/update"> -->
              <a
                href=""
                data-toggle="tooltip"
                data-placement="left"
                title="Update Profile"
              >
                <img
                  :src="`img/public/default.jpg`"
                  class="rounded-circle nav-link"
                  width="50px"
                  height="50px"
                />
              </a>
            
          </div>
          </router-link>
        </li>
      </ul>

      <ul class="navbar-nav" v-if="$auth.check()">
        <li class="nav-item" style="">
          <a class="nav-link" href="#" style="" @click.prevent="$auth.logout()"
            >Logout</a
          >
        </li>
      </ul>
	  
	  
	  
<style>
/* .navbar {
  margin-bottom: 30px;
} */

.overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  height: 100%;
  width: 100%;
  opacity: 0;
  transition: 0.5s ease;
  background-color: #008cba;
}

.container:hover .overlay {
  opacity: 1;
}

.text {
  color: white;
  font-size: 20px;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  text-align: center;
}

.image {
  transition: 0.5s ease;

  background-color: black;
}
.image:hover {
  background-color: rgb(85, 100, 100);
}

nav {
  background: black;
}

a {
  color: white;
}
</style>



13. add a route to show all things of user in frontend
  Route::get('show', 'App\Http\Controllers\AuthController@index');
  
  
14. now i wanna update profile image so make a updateFunction in controller 


    public function update(Request $request, $id)
    {
        $upload = user::find($id);

        $this->validate($request,[
            
            'photo' => 'required'
        ]);
  
        $currentPhoto = $upload->photo;  // $currentPhoto is that image there are in table and file of framework
  
        if($request->photo != $currentPhoto){
             //  this code make image or convert file in controller and for play that i install require image in First
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/public/').$name);  // this code add image that we upload in folder (public/img/public)
            $request->merge(['photo' => $name]);
  
            $userPhoto = public_path('img/public/').$currentPhoto;
  
            if(file_exists($userPhoto)){   // if file or image exist in publucFolder move and add the new image 
  
                @unlink($userPhoto);
                
            }
           
        }
  
        $upload->update($request->all());
  
        return ['message' => 'Success'];
    } 
	
15. make a route to update profile of user 

  Route::post('update/{id}', 'App\Http\Controllers\AuthController@update');

16. make a page for update of profile user to name update.vue


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
            <div class="mr-auto mb-auto" v-if="book.photo">
              <img
                :src="`img/public/${book.photo}`"
                class="nav-link rounded-circle"
                width="150px"
                height="150px"
              />
            </div>
            <div v-else>
              <img
                :src="`img/public/default.jpg`"
                class="rounded-circle nav-link"
                width="150px"
                height="150px"
              />
            </div>
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

      imageprevi: "",
      image: null,
    };
  },
  mounted() {
    this.getResults();
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

    login(id) {
      this.form
        .post("../v1/auth/update/" + id)

        .then(() => {
          Toast.fire({
            icon: "success",
            title: "File uploaded successfully",
          });
          $(".upload").hide();
          this.getResults();
          // this.axios
          //   .get("../v1/auth/show")

          //   .then((response) => {
          //     this.books = response.data.data;
          //     console.log(this.books)
          //   })
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






17. add the router of vue this page


thanks






 	

    
	  
