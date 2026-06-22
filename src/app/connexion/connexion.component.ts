import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-connexion',
  templateUrl: './connexion.component.html',
})
export class ConnexionComponent {
  error = '';

  constructor(
    private http: HttpClient,
    private router: Router,
  ) {}

  connecter(email: string, mdp: string) {
    this.http.post<any>('/api/connexion.php', { email, mdp }).subscribe((data) => {
      if (data.success) {
        // Log de connexion réussie
        this.http
          .post('http://localhost:3000/api/logs/create', {
            userId: email,
            action: 'login',
          })
          .subscribe();

        localStorage.setItem('user', JSON.stringify({ role: data.role, email: email }));
        this.router.navigate(['/catalogue']);
      } else {
        // Log de tentative échouée
        this.http
          .post('http://localhost:3000/api/logs/create', {
            userId: email,
            action: 'login_failed',
          })
          .subscribe();

        this.error = data.error;
      }
    });
  }
}
