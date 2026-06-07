import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';

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

  constructor(private router: Router) {}

  search(value: string) {
    this.router.navigate(['/catalogue'], { queryParams: { search: value } });
  }

  deconnexion() {
    localStorage.removeItem('user');
    this.router.navigate(['/connexion']);
  }
}