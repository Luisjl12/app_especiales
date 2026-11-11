// Pequeño ayudante para llamar a Laravel con el token CSRF
let token = document.querySelector('meta[name="csrf-token"]').content;

export function setToken(nuevo) {
  token = nuevo;
}

export async function api(url, method = 'GET', body = null) {
  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': token,
    },
    body: body ? JSON.stringify(body) : null,
  });

  const datos = await res.json().catch(() => ({}));
  if (!res.ok) throw datos;
  return datos;
}

export async function apiArchivo(url, formData)
{
  const res=await fetch(url,{
    method: 'POST', 
    headers: {
      'Accept': 'application/json', 
      'X-CSRF-TOKEN': token,
    }, 
    body: formData, 
  }); 
  
  const datos = await res.json().catch(() => ({}));
  if (!res.ok) throw datos;
  return datos;
}