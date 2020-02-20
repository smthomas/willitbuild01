<?php

namespace Drupal\gatsby;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\jsonapi_extras\EntityToJsonApi;

/**
 * Class GatsbyPreview.
 */
class GatsbyPreview {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $httpClient;

  /**
   * Config Interface for accessing site configuration.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $logger;

  /**
   * Drupal\jsonapi_extras\EntityToJsonApi definition.
   *
   * @var \Drupal\jsonapi_extras\EntityToJsonApi
   */
  private $entityToJsonApi;

  /**
   * Constructs a new WebinarSync object.
   */
  public function __construct(ClientInterface $http_client,
      ConfigFactoryInterface $config,
      EntityTypeManagerInterface $entity_type_manager,
      LoggerChannelFactoryInterface $logger,
      EntityToJsonApi $entity_to_json_api) {
    $this->httpClient = $http_client;
    $this->config = $config->get('gatsby.settings');
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger->get('gatsby');
    $this->entityToJsonApi = $entity_to_json_api;
  }

  /**
   * Send updates to Gatsby Preview server.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to update.
   */
  public function updatePreviewEntity(ContentEntityInterface $entity) {
    $encoded_json = $this->entityToJsonApi->serialize($entity);

    // If there is a secret key, we decode the json, add the key, then encode.
    if ($this->config->get('secret_key')) {
      $json_object = json_decode($encoded_json);
      $json_object->secret = $this->config->get('secret_key');
      $encoded_json = json_encode($json_object);
    }

    $server_url = $this->config->get('server_url');

    try {
      $this->httpClient->post(
        $server_url . "/___updatePreview",
        [
          'json' => $encoded_json,
          'timeout' => 1,
        ]
      );
    }
    catch (ServerException | ConnectException $e) {
      // Do nothing as no response is returned from the preview server.
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }
  }

  /**
   * Send delete request to Gatsby Preview server.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to delete.
   */
  public function deletePreviewEntity(ContentEntityInterface $entity) {
    $server_url = $this->config->get('server_url');

    try {
      $data = [
        'id' => $entity->uuid(),
        'action' => 'delete',
      ];

      // If there is a secret key, add the key to the request.
      if ($this->config->get('secret_key')) {
        $data['secret'] = $this->config->get('secret_key');
      }

      $this->httpClient->post(
        $server_url . "/___updatePreview",
        [
          'json' => json_encode($data),
          'timeout' => 1,
        ]
      );
    }
    catch (ServerException | ConnectException $e) {
      // Do nothing as no response is returned from the preview server.
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }
  }

  /**
   * Verify the entity is selected to sync to the Gatsby site.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   *
   * @return bool
   *   If the entity type should be sent to Gatsby Preview.
   */
  public function isPreviewEntity(ContentEntityInterface $entity) {
    $entityType = $entity->getEntityTypeId();
    $selectedEntityTypes = $this->config->get('preview_entity_types') ?: [];
    return in_array($entityType, array_values($selectedEntityTypes), TRUE);
  }

}
