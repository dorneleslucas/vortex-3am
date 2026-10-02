const faqsRegisterForm = document.querySelector('#faqs-register-form'); 

if(faqsRegisterForm) { 
    faqsRegisterForm.addEventListener('submit' , async(event )=> { 
        event.preventDefault(); 

        const response = await fetch("/vortex-3am/api/faqs/list" , {
            method: "POST", 
            headers: { 
                Authorization: `Bearer ${token}`
            }, 
            body: new FormData(faqsRegisterForm)
        });

        const result = await response.json();
        console.log(result);
    })
}