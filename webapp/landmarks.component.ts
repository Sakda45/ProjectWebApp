import { Component } from '@angular/core';
import jsonData from '../../../assets/landmark.json'
import {MatFormFieldModule} from '@angular/material/form-field';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-landmarks',
  standalone: true,
  imports: [MatFormFieldModule, FormsModule],
  templateUrl: './landmarks.component.html',
  styleUrl: './landmarks.component.scss'
})
export class LandmarksComponent {
  landmarks = jsonData;
  landmark : any
  countrySet = new Set<string>();
  countries : any;
 constructor(){
  // console.log(jsonData)
  this.landmark = this.landmarks[0];
  this.landmarks.forEach(element => {
    this.countrySet.add(element.country);
  });
  this.countries = Array.from(this.countrySet);
  console.log(this.countries);
}
Search(id : any){
   for(let index = 0; index < this.landmarks.length; index++){
    if(this.landmarks[index].idx == id){
      this.landmark = this.landmarks[index]
    }
   }
}
}

