
Vue.component("review", {
  props: ["review"],
  data(){
    return {
      user : this.review.user,
      rating : this.review.review,
    };
  },
  methods:{
    stars(){
      if (this.rating.rating == 0 || this.rating.rating == null) {
        return 1;
      }else{
        return parseInt(this.rating.rating)
      } 
    } 
  }, 
  computed: {
  }, 
  
  template: `
    <div class="col-sm-4">
			<a class="card card-flush bg-light-success hoverable min-h-125px shadow-none mb-5">
				<div class="card-body d-flex flex-column flex-center position-relative pb-2">
				  <div class="">
				    <div class="">
				    <div class="d-flex align-items-center mb-7">
							<!--begin::Symbol-->
							<div class="symbol symbol-45px me-10">
								<span class="symbol-label bg-light align-items-end">
									<img alt="Image" :src="user.image" class="mh-50px rounded" />
								</span>
							</div>
							<!--end::Symbol-->
							<!--begin::Info-->
							<div class="d-flex flex-column flex-grow-1">
								<a class="text-gray-800 text-hover-primary mb-0 fs-3 fw-bolder">
								  {{user.name}} 
								</a>
								<span class="text-muted fw-bold">
  								@{{user.screen_name}} 
								</span>
							</div>
						</div>
						<div class="mb-3" >
						  <i v-for="index in stars()" class="bi-star-fill text-warning"></i>
						  <i v-for="index in (5-stars())" class="bi-star text-dark"></i>
						</div>
						</div>
						<p class="fs-6 text-dark">
						  {{rating.text}} 
						  <br/>
						  <small class="blockquote-footer float-right text-muted fw-bold">
						    {{review.created_at}} 
						  </small>
						</p>
				  </div>
				  <h5 class="fw-bolder text-dark mb-2 text-center ">Business: {{rating.business}}</h5>
				</div>
			</a>
		</div>
  `
})

let app = new Vue({
  el: "#app", 
  data: {
    reviews: [
      {
        created_at: "18-12-2020", 
        review: {
          text: "I loved the way you keep me from being a good friend of mine ", 
          rating: 3,
          business: "Alye fc"
        }, 
        user: {
         id : "124532245", 
         name: "James ", 
         screen_name: "jui", 
         image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRFqHrEFHx0cWLPD11oQQD5epeSt54MwUuI1A&usqp=CAU"
        }, 
        tweet: "I think I have a sample ui on this topic that can be customized and used for faster delivery "
      }
    ], 
    app_name: "Buddie"
  },
  computed: {
    app(){
      return 'Hi, I am ' + this.app_name ;
    } 
  },
  mounted(){
    let sse = new EventSource('public/data.php');
  	setTimeout(()=> {
    	sse.addEventListener('ScheduleEvent', (e)=>{
    		let data = e.data;
    		console.log(JSON.parse(data));
    		this.reviews.push(...JSON.parse(data));
    		//handle your data here
    	},false); 
      console.log("Fetching tweets... ")
  	}, 3000);
  	
  	sse.onerror = (e) => {
      console.log("An error occurred while attempting to connect. ", e);
    };
  }, 
  beforeMount(){
    /*$.ajax({
      url: "public/data.php",
      method: "POST", 
      success: (e)=>{
        this.reviews.push(...JSON.parse(e))
        console.log();
      }, 
      error: (e)=>{
        console.log(e);
      }, 
    })*/
  } 
  // END OF DATA
})