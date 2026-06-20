import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { Subscription } from 'rxjs';
import { debounceTime } from 'rxjs/operators';
import { SearchService } from '../search.service'; // ajuste le chemin selon où tu places search.service.ts

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-catalogue',
  templateUrl: './catalogue.component.html'
})
export class CatalogueComponent implements OnInit, OnDestroy {
  produits: any[] = [];
  categories: any[] = [];
  private searchSub!: Subscription;

  constructor(
    private http: HttpClient,
    private cdr: ChangeDetectorRef,
    private searchService: SearchService,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    // Recherche initiale via query param si présent (lien direct/partagé)
    const initialSearch = this.route.snapshot.queryParams['search'] || '';
    this.charger({ search: initialSearch });

    // Écoute les changements de recherche en live, avec debounce pour éviter
    // de spammer l'API à chaque caractère tapé trop vite
    this.searchSub = this.searchService.searchTerm$
      .pipe(debounceTime(300))
      .subscribe((value: string) => {
        this.charger({ search: value });
      });
  }

  ngOnDestroy() {
    this.searchSub?.unsubscribe();
  }

  charger(params: any) {
    this.http.get<any>('/api/catalogue.php', { params }).subscribe((data: any) => {
      this.produits = data.produits;
      this.categories = data.categories;
      this.cdr.detectChanges();
    });
  }
}