<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS 
    <link href="dist/css/bootstrap.min.css" rel="stylesheet" >
    -->
    <link rel="stylesheet" href="assets/style.bundle.css" type="text/css" media="all" />
    
    <link rel="stylesheet" href="assets/bootstrap-icons/font/bootstrap-icons.css" type="text/css" media="all" />
    <style type="text/css" media="all">
    
    </style>
    <script src="assets/node_modules/eruda/eruda.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
      eruda.init()
    </script>
    <title>Bot review</title>
  </head>
  <body>
    <div id="app" class="container mb-5 ">
      <div class="d-flex flex-column min-vh-100 flex-center">
        <header>
          <h2 class="display-2 text-center mb-4 pb-3">
            {{app}} 
          </h2>
          <div class="row justify-content-center">
            <div align="center" class="col-md-4 text-center col-lg-3">
              <h4 v-if="reviews.length < 0">Fetching tweets</h4>
              <div class="separator mx-auto border-4 w-75 border-secondary my-3"></div>
              <div class="separator mx-auto border-4 w-75 border-secondary my-3"></div>
              <div class="separator mx-auto border-4 w-75 border-secondary my-3"></div>
            </div> 
          </div>
        </header>
        
        <main>
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="card">
                <div :class="(reviews.length > 0) ? '':'d-flex flex-column flex-center' " class="card-body mb-4">
                  <div class="input-group mb-10">
                    <input type="text" class="form-control border-right-0" placeholder="search id or name" aria-label="search">
                    <button class="input-group-text border border-secondary border-left-0 btn btn-white">
                      <i class="bi-search"></i>
                    </button>
                  </div>
   
                  <div v-if="reviews.length > 0" class="row g-5">
  							    <review v-for="(review, index) in reviews" :review="review" :key="index"></review>
  							  </div>
  							  <div v-else class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
    
    
    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="assets/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="assets/vue.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="assets/main.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
      
    </script>
  </body>
</html>

