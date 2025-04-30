document.addEventListener("DOMContentLoaded", () => {
    // Handle room type selection
    const roomTypeLinks = document.querySelectorAll(".dropdown-item")
  
    roomTypeLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault()
  
        // Update dropdown button text
        const selectedText = this.textContent
        document.getElementById("roomTypeDropdown").textContent = selectedText
  
        // In a real implementation, you would show the selected room type
        // and hide others, or fetch the room data from an API
  
        // For demonstration, we'll just scroll to the section
        const targetId = this.getAttribute("href").substring(1)
        const targetElement = document.getElementById(targetId)
  
        if (targetElement) {
          targetElement.scrollIntoView({ behavior: "smooth" })
        }
      })
    })
  
    // For a complete implementation, you would add code here to:
    // 1. Load all 5 room types from a data source
    // 2. Handle the display of different room types when selected
    // 3. Implement the share functionality
    // 4. Add booking and exploration functionality
  
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
    const mobileMenuClose = document.querySelector(".mobile-menu-close")
    const mobileMenu = document.querySelector(".mobile-menu")
  
    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener("click", () => {
        mobileMenu.classList.add("active")
        document.body.style.overflow = "hidden"
      })
    }
  
    if (mobileMenuClose) {
      mobileMenuClose.addEventListener("click", () => {
        mobileMenu.classList.remove("active")
        document.body.style.overflow = ""
      })
    }
  
    // Navbar Scroll Effect
    const navbar = document.querySelector(".navbar")
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled")
      } else {
        navbar.classList.remove("scrolled")
      }
    })
  
    // Enhanced Room Carousel
    const roomCarousel = document.querySelector(".room-carousel")
    const roomSlides = document.querySelectorAll(".room-slide")
    const roomPrevControl = document.querySelector(".room-carousel-control.prev")
    const roomNextControl = document.querySelector(".room-carousel-control.next")
    const roomDots = document.querySelectorAll(".room-dot")
    const roomDescriptionHeader = document.querySelector(".room-description h3")
    const roomDescriptionParagraph = document.querySelector(".room-description p")
  
    let roomIndex = 0
    const roomDescriptions = [
      {
        title: "Dexule room with private balcony",
        description:
          "The centerpiece of this luxurious suite with 91 square meters of interiors is a large private terrace where you can experience private dining. Enjoy panoramic views of Lake Pichola and the City Palace while indulging in the finest amenities and personalized service.",
      },
      {
        title: "Luxury room with private balcony",
        description:
          "This elegant suite offers lush garden views, a cozy sitting area, and modern smart room amenities for a perfect retreat. The 75 square meter space includes a private balcony where you can enjoy your morning coffee surrounded by nature.",
      },
      {
        title: "Premier room with private balcony",
        description:
          "With panoramic views and a spacious balcony, this 65 square meter suite is perfect for relaxing in style with cutting-edge tech comforts. The suite features a king-sized bed, a separate living area, and a luxurious marble bathroom.",
      },
      {
        title: "Executive suite with private balcony",
        description:
          "Our Premier Rooms offer 55 square meters of sophisticated comfort with garden views. Each room features elegant décor, a plush king-sized bed, and a private balcony where you can enjoy the serene surroundings.",
      },
      {
        title: "Deluxe suite",
        description:
          "The Deluxe Room provides 45 square meters of comfort with city views. Perfect for business travelers or couples, these rooms feature modern amenities, a work desk, and a cozy seating area for relaxation.",
      },
    ]
  
    function updateRoomCarousel() {
      // Update slide positions
      roomSlides.forEach((slide, index) => {
        slide.style.transform = index === roomIndex ? "scale(1)" : "scale(0.9)"
        slide.style.opacity = index === roomIndex ? "1" : "0.7"
        slide.classList.toggle("active", index === roomIndex)
      })
  
      // Update carousel position
      const slideWidth = roomSlides[0].offsetWidth
      roomCarousel.style.transform = `translateX(${-roomIndex * slideWidth}px)`
  
      // Update dots
      roomDots.forEach((dot, index) => {
        dot.classList.toggle("active", index === roomIndex)
      })
  
      // Update description
      roomDescriptionHeader.textContent = roomDescriptions[roomIndex].title
      roomDescriptionHeader.style.animation = "none"
      roomDescriptionParagraph.textContent = roomDescriptions[roomIndex].description
      roomDescriptionParagraph.style.animation = "none"
  
      // Trigger reflow
      void roomDescriptionHeader.offsetWidth
      void roomDescriptionParagraph.offsetWidth
  
      // Add animation
      roomDescriptionHeader.style.animation = "fadeIn 0.5s ease"
      roomDescriptionParagraph.style.animation = "fadeIn 0.7s ease"
    }
  
    // Room carousel navigation
    if (roomPrevControl) {
      roomPrevControl.addEventListener("click", () => {
        roomIndex = (roomIndex - 1 + roomSlides.length) % roomSlides.length
        updateRoomCarousel()
      })
    }
  
    if (roomNextControl) {
      roomNextControl.addEventListener("click", () => {
        roomIndex = (roomIndex + 1) % roomSlides.length
        updateRoomCarousel()
      })
    }
  
    // Room carousel dots
    roomDots.forEach((dot) => {
      dot.addEventListener("click", () => {
        roomIndex = Number.parseInt(dot.getAttribute("data-index"))
        updateRoomCarousel()
      })
    })
  
    // Initialize room carousel
    if (roomSlides.length > 0) {
      updateRoomCarousel()
    }
  
    // Restaurant Carousel
    const restaurantSections = document.querySelectorAll('[id^="restaurant-section-"]')
  
    restaurantSections.forEach((section) => {
      const sectionId = section.id
      const prevBtn = document.getElementById(`prev-btn-${sectionId.split("-").pop()}`)
      const nextBtn = document.getElementById(`next-btn-${sectionId.split("-").pop()}`)
      const carouselItems = section.querySelectorAll(".carousel-items .carousel-item")
      let currentIndex = 0
  
      // Function to show the current slide
      function showSlide(index) {
        // Hide all slides
        carouselItems.forEach((item) => {
          item.classList.remove("active")
        })
  
        // Show the current slide
        carouselItems[index].classList.add("active")
      }
  
      // Event listeners for navigation buttons
      if (prevBtn) {
        prevBtn.addEventListener("click", () => {
          currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length
          showSlide(currentIndex)
        })
      }
  
      if (nextBtn) {
        nextBtn.addEventListener("click", () => {
          currentIndex = (currentIndex + 1) % carouselItems.length
          showSlide(currentIndex)
        })
      }
  
      // Initialize the carousel
      showSlide(currentIndex)
    })
  
    // Booking Form Date Validation
    const checkinDate = document.getElementById("checkin_date")
    const checkoutDate = document.getElementById("checkout_date")
  
    if (checkinDate && checkoutDate) {
      // Set minimum date to today
      const today = new Date()
      const dd = String(today.getDate()).padStart(2, "0")
      const mm = String(today.getMonth() + 1).padStart(2, "0")
      const yyyy = today.getFullYear()
      const todayString = `${yyyy}-${mm}-${dd}`
  
      checkinDate.setAttribute("min", todayString)
  
      // Update checkout min date when checkin changes
      checkinDate.addEventListener("change", function () {
        checkoutDate.setAttribute("min", this.value)
  
        // If checkout date is before checkin date, update it
        if (checkoutDate.value && checkoutDate.value < this.value) {
          checkoutDate.value = this.value
        }
      })
    }
  
    // Add animation classes on scroll
    const animateOnScroll = () => {
      const elements = document.querySelectorAll(".animate-on-scroll")
  
      elements.forEach((element) => {
        const elementPosition = element.getBoundingClientRect().top
        const windowHeight = window.innerHeight
  
        if (elementPosition < windowHeight - 100) {
          element.classList.add("animated")
        }
      })
    }
  
    // Add animate-on-scroll class to sections
    document
      .querySelectorAll(".welcome-rajasthan-section, .room-section, .restaurant-section, .events-section")
      .forEach((section) => {
        section.classList.add("animate-on-scroll")
      })
  
    window.addEventListener("scroll", animateOnScroll)
  
    // Initialize animations
    animateOnScroll()
  })
  