const { default: axios } = require('axios');

require('./bootstrap');

console.log("hola");

const post = document.getElementById("post");
const addtext = document.getElementById("addtext-button");
const addimg = document.getElementById("addimg-button");
const post_button = document.getElementById("post-button");

let elements_count = 0;
let elements = [];

addtext.addEventListener("click", function(){
    const externalDiv = document.createElement("div");
    externalDiv.className="post-externalDiv"
    const div = document.createElement("div");
    div.className = "post-text"
    const textinput = document.createElement("textarea");
    textinput.id = "element"+elements_count;
    elements.push(textinput);
    elements_count++;
    const removeText = document.createElement("img");
    removeText.src= "/img/trash-alt-solid.svg";
    removeText.className="remove-text-img";
    div.appendChild(textinput);
    externalDiv.appendChild(div);
    externalDiv.appendChild(removeText);
    post.appendChild(externalDiv);

    removeText.addEventListener("click", function(){
        elements.splice(elements.indexOf(this.parentNode.children[0].children[0]), 1);
        console.log(elements);
        this.parentNode.remove();
    });
})

addimg.addEventListener("click", function(){
    const externalDiv = document.createElement("div");
    externalDiv.className="post-externalDiv"
    const div = document.createElement("div");
    div.className = "post-div"
    const imginput = document.createElement("input");
    imginput.id = "element"+elements_count;
    elements.push(imginput);
    elements_count++;
    imginput.type = "file";
    imginput.accept = "image/*";
    let image = document.createElement("img");
    const img_div = document.createElement("div");
    img_div.className = "post-img-div";
    imginput.addEventListener("change", function(e){
        image.src = URL.createObjectURL(e.target.files[0]);
        image.className = "post-img";
        img_div.appendChild(image);
    })
    
    const removeText = document.createElement("img");
    removeText.src = "/img/trash-alt-solid.svg";
    removeText.className ="remove-text-img";
    div.appendChild(imginput);
    div.appendChild(img_div);
    externalDiv.appendChild(div);
    externalDiv.appendChild(removeText);
    post.appendChild(externalDiv);

    removeText.addEventListener("click", function(){
        elements.splice(elements.indexOf(this.parentNode.children[0].children[0]), 1);
        console.log(elements);
        this.parentNode.remove();
    });
})

post_button.addEventListener("click", function(){
    let images = [];
    let texts = [];
    let formData = new FormData();
    let imgIndex = 0;
    let txtIndex = 0;
    elements.forEach(function(element, pos){
        if(element.type=="file"){
            formData.append(`images[${imgIndex}][img]`, element.files[0]);
            formData.append(`images[${imgIndex}][pos]`, pos);
            imgIndex++;
            //images.push({element: element.files[0], index: index});
        }else{
            formData.append(`texts[${txtIndex}][txt]`, element.value);
            formData.append(`texts[${txtIndex}][pos]`, pos);
            txtIndex++;
            //texts.push({element: element.value, index: pos});
        }
    })

    formData.append("title", document.getElementById("title").value);
    //formData.append("texts[]", texts);
    console.log(images);
    axios.post("/posts",formData,{
        headers: {
            'Content-Type': 'multipart/form-data',
            //'Accept': '*/*',
        }
    })
    .then(res => {
        console.log(res.data);
        window.location.href='../home';
    }).catch(err => {
        console.log(err);
    })

});