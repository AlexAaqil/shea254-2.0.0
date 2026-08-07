import './bootstrap';

// This code listens for events from Livewire components
// When Livewire dispatches an event, this code catches it and triggers the Pixel

document.addEventListener('DOMContentLoaded', function() {
    
    // Listen for ViewContent event (when someone views a product)
    window.addEventListener('track-view-content', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = event.detail;
            const eventId = eventData.event_id;
            delete eventData.event_id;

            fbq('track', 'ViewContent', eventData, {eventID: eventId});
            console.log('Meta Pixel: ViewContent tracked with event_id:', eventId);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    // Listen for AddToCart event (when someone adds to cart)
    window.addEventListener('track-add-to-cart', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = event.detail;
            const eventId = eventData.event_id;
            delete eventData.event_id;

            fbq('track', 'AddToCart', eventData, {eventID: eventId});
            console.log('Meta Pixel: AddToCart tracked with event_id:', eventId);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    // Listen for InitiateCheckout event (when someone starts checkout)
    window.addEventListener('track-initiate-checkout', function(event) {
        if (typeof fbq !== 'undefined') {
            fbq('track', 'InitiateCheckout', event.detail);
            console.log('Meta Pixel: InitiateCheckout tracked', event.detail);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    // Listen for Purchase event (MOST IMPORTANT - when someone buys)
    window.addEventListener('track-purchase', function(event) {
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Purchase', event.detail);
            console.log('Meta Pixel: Purchase tracked', event.detail);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    // Listen for any custom event (flexibility for other events)
    window.addEventListener('track-meta-event', function(event) {
        if (typeof fbq !== 'undefined') {
            const { eventName, eventData } = event.detail;
            fbq('track', eventName, eventData);
            console.log(`Meta Pixel: ${eventName} tracked`, eventData);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
});
