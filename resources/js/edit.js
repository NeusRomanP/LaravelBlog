const { default: axios } = require('axios');
const { forEach } = require('lodash');

require('./bootstrap');

const post = document.getElementById("post");
const addtext = document.getElementById("addtext-button");
const addimg = document.getElementById("addimg-button");
const post_button = document.getElementById("post-button");
const remove_buttons = document.getElementsByClassName("remove-text-img");
let els = document.getElementsByClassName("post-element");

let elements_count = 0;
let elements = [];
let deleted_imgs = [];
let deleted_txts = [];
let updated_imgs = [];

function getElements(){
    
    for(let i = 0; i<els.length;i++){
        if(els[i].src){
            let src = {
                content: "/img" + els[i].src.split("/img")[1],
                type: "img",
                id: i
            }
            elements.push(src);
        }else{
            let txt = {
                content: els[i].innerHTML,
                type: "txt",
                id: i
            }
            elements.push(txt);
        }
    }
}

function addRemove(){
    for(let i = 0; i < remove_buttons.length; i++){
        remove_buttons[i].addEventListener("click", function(){
            let el = this.parentNode.children[0].children[0];
            if(el.type=="file"){
                let img = el.parentNode.children[1].children[0];
                let src = "/img" + img.src.split("/img")[1];
                
                deleted_imgs.push(src);
                elements.splice(elements.map(function(x) {return x.id; }).indexOf(parseInt(img.id)), 1);
                this.parentNode.remove()
            }else{
                deleted_txts.push(elements[elements.map(function(x) {return x.id; }).indexOf(parseInt(this.parentNode.children[0].children[0].id))]);
                elements.splice(elements.map(function(x) {return x.id; }).indexOf(parseInt(this.parentNode.children[0].children[0].id)), 1);
                this.parentNode.remove();
            }

            elements.forEach((element, index) => {
                element["id"] = index;
            });
            
        })
    }
}

function addChangeImg(){
    let els = document.querySelectorAll('input[type=file]')
    let index = 0;
    elements.forEach(element => {
        if(element["type"]=="img"){
            els[index].addEventListener("change", function(e){
                let former_img = element;
                element.img = URL.createObjectURL(e.target.files[0]);
                let img = {
                    img: former_img['content'],
                    file: this.files[0],
                    pos: index
                }
                updated_imgs.push(img);
                this.parentNode.children[1].children[0].src = URL.createObjectURL(e.target.files[0]);
            })
            index ++;
        }
    });
    els.forEach(el => {
        
    });
    
}

getElements();
addRemove();
addChangeImg();



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
        }else if(element["type"] == "img"){
            formData.append(`images[${imgIndex}][img]`, element["content"]);
            formData.append(`images[${imgIndex}][pos]`, pos);
            imgIndex++;
        }else if(element["type"] == "txt"){
            let post_element = document.getElementsByClassName("post-element")[pos];
            element["content"] = els[pos].value;
            formData.append(`texts[${txtIndex}][txt]`, element["content"]);
            formData.append(`texts[${txtIndex}][pos]`, pos);
            txtIndex++;
        }else{
            formData.append(`texts[${txtIndex}][txt]`, element.value);
            formData.append(`texts[${txtIndex}][pos]`, pos);
            txtIndex++;
        }
    })
    deleted_imgs.forEach(function(deleted_img, pos){
        formData.append(`deleted_imgs[${pos}][img]`, deleted_img);
    })

    updated_imgs.forEach(function(updated_img, pos){
        formData.append(`updated_imgs[${pos}][img]`, updated_img["img"]);
        formData.append(`updated_imgs[${pos}][pos]`, updated_img["pos"]);
        formData.append(`updated_imgs[${pos}][file]`, updated_img["file"]);
    })

    

    deleted_txts.forEach(function(deleted_txt, pos){
        formData.append(`deleted_txts[${pos}][txt]`, deleted_txt['txt']);
        formData.append(`deleted_txts[${pos}][id]`, deleted_txt['id']);
    })

    formData.append("title", document.getElementById("title").value);
    let location=window.location.href.split("/")
    axios.post("/posts/"+location[location.length-1],formData,{
        headers: {
            'Content-Type': 'multipart/form-data',
        }
    })
    .then(res => {
        window.location.href='../../home';
    }).catch(err => {
        console.log(err);
    })

});