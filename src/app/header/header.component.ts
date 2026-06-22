import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { SearchService } from '../search.service'; // ajuste le chemin selon où tu places search.service.ts

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-header',
  templateUrl: './header.component.html'
})
export class HeaderComponent {
  get isLoggedIn() {
    return !!localStorage.getItem('user');
  }

  constructor(private router: Router, private searchService: SearchService) {}

  search(value: string) {
    // Si on n'est pas déjà sur le catalogue, on y navigue d'abord
    if (this.router.url !== '/catalogue') {
      this.router.navigate(['/catalogue']);
    }
    this.searchService.updateSearch(value);
  }

  deconnexion() {
    localStorage.removeItem('user');
    this.router.navigate(['/connexion']);
  }
}