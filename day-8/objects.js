//create an obj from existing obj
const person = {
    fname : "xyz",
    lname: "abc",
    language: "EN"
};

const man = Object.create(person);
man.fname = "ghj";

console.log(person.fname + " " + man.fname);

//Object.assign() = copies properties from one on more source objs to target objs

//target obj
const p1 = {
    fname: "abc",
    lname: "cde",
    age: 16,
    eyeColour: "blue"
}

//source obj
const p2 = {
    fname: "ghi",
    lname: "jkl"
}

let text = Object.assign(p1, p2);

console.log(text);

//constructors
function Family(first, last, age){
    this.firstName = first;
    this.lastName = last;
    this.age = age;
    this.nationality = "Indian"
    this.fullname = function(){
        return this.firstName + " " + this.lastName;
    };
}

const father = new Family("Ashok", "Vaghela", 48);
const mother = new Family("Jayshree", "Vaghela", 42);
const mySelf = new Family("Sneha", "Vaghela", 20);



console.log(Object.entries(father));
father.nationality = "Russian";
console.log(Object.entries(father));





