import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-catalogue',
  templateUrl: './catalogue.component.html'
})
export class CatalogueComponent implements OnInit {
  produits: any[] = [];
  categories: any[] = [];

  constructor(private http: HttpClient, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.charger({});
  }

  charger(params: any) {
    this.http.get<any>('/api/catalogue.php', { params }).subscribe((data: any) => {
      this.produits = data.produits;
      this.categories = data.categories;
      this.cdr.detectChanges();
    });
  }
}