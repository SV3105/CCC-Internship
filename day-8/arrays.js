const person = ["abc", "xyz", "fgh"];
console.log(typeof(person));

//typeof array is obj so, to recognize array --> isArray() & instanceof
console.log(Array.isArray(person));

console.log(person instanceof Array);

//splice method
const fruits = ["Banana", "Orange", "Apple", "Mango"];
fruits.splice(2, 0, "Lemon", "Kiwi");

// 1st parameter -> where to add new ele.
//2nd parameter -> how many ele. should removed

//The difference between the new toSpliced() method and the old splice() method is that the new method creates a new array, keeping the original array unchanged, while the old method altered the original array.

console.log(fruits.includes("Apple"));

const numbers = [14, 7, 1, 39, 50];

//find -> value, findIndex -> index , findLast()-> finds from end , findLastIndex()
let first = numbers.find(test);

function test(value, index, array){
    return value > 18;
}

console.log(first);

//sort() -> sorts values as strings 
//in numbers "25" is bigger than "100" so it will gives incorrect result

//solution 
numbers.sort(function(a,b){return a-b})

//to create an array from string or any obj
let text = "abcdefgh";
console.log(Array.from(text));

let fruit = fruits.entries()
for(let x of fruit){
    console.log(x);  
}

