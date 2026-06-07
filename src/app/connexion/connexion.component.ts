import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-connexion',
  templateUrl: './connexion.component.html'
})
export class ConnexionComponent {
  error = '';

  constructor(private http: HttpClient, private router: Router) {}

  connecter(email: string, mdp: string) {
    this.http.post<any>('/api/connexion.php', { email, mdp }).subscribe(data => {
      if (data.success) {
        localStorage.setItem('user', JSON.stringify({ role: data.role }));
        this.router.navigate(['/catalogue']);
      } else {
        this.error = data.error;
      }
    });
  }
}